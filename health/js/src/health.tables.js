(function ($) {
  Drupal.behaviors.healthTables = {
    attach: function (context, settings) {
      // Find the footers in tables.
      $('.region-content tfoot').each(function() {
        var maxCols = 1;
        // Get the table.
        var $table = $(this).parents('table');
        // Find all rows.
        $table.find('tr').each(function() {
          // Find how many columns there are in this row.
          var cols = $(this).find('td,th').length;
          // If it is more than the max, store it.
          if (maxCols < cols) {
            maxCols = cols;
          }
        });

        // Add the max columns to the footer so it spans the width of the table.
        $(this).find('td').attr('colspan', maxCols);
      });
    }
  };
})(jQuery);