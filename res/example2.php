<?php 

$page   = $_GET['page']; 
$limit  = $_GET['rows']; 
$sidx   = $_GET['sidx']; 
$sord   = $_GET['sord']; 

$data = array();

$data[] = array(
  'invid'   => 1,
  'invdate' => '28.05.10',
  'amount'  => 100,
  'tax'     => 10,
  'total'   => 110,
  'note'    => 'Dies ist ein Hinweistext',
);
$data[] = array(
  'invid'   => 2,
  'invdate' => '27.05.10',
  'amount'  => 35,
  'tax'     => 4,
  'total'   => 40,
  'note'    => 'Dies ist auch ein Hinweistext',
);
$data[] = array(
  'invid'   => 3,
  'invdate' => '26.05.10',
  'amount'  => 9,
  'tax'     => 3,
  'total'   => 12,
  'note'    => sprintf('page=%s | limit=%s | sidx=%s | sort=%s',$page,$limit,$sidx,$sort),
);

header("Content-type: text/xml;charset=utf-8");
 
$s  = "<?xml version='1.0' encoding='utf-8'?>";
$s .= "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>1</total>";//pages
$s .= "<records>".count($data)."</records>";
foreach( $data as $i => $row ){
  $s .= "<row id='". $row[invid]."'>";            
  $s .= "<cell>". $row[invid]."</cell>";
  $s .= "<cell>". $row[invdate]."</cell>";
  $s .= "<cell>". $row[amount]."</cell>";
  $s .= "<cell>". $row[tax]."</cell>";
  $s .= "<cell>". $row[total]."</cell>";
  $s .= "<cell><![CDATA[". $row[note]."]]></cell>";
  $s .= "</row>";
}
$s .= "</rows>"; 

echo $s;
?>