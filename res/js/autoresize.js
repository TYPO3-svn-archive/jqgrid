
$(window).bind('resize', function() {
  if (grid = $('.ui-jqgrid-btable:visible')) {
    grid.each(function(index) {
      gridId = $(this).attr('id');
      gridParentWidth = $('#gbox_' + gridId).parent().width();
      $('#' + gridId).setGridWidth(gridParentWidth);
    });
  }
}).trigger('resize');
