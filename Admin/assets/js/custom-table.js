$(document).ready(function () {
    $('.custom-table').DataTable({
        "paging": true,             // Enable pagination
        "searching": true,          // Enable search
        "ordering": true,           // Enable sorting
        "pageLength": 10,           // Default records per page
        "lengthChange": false,      // Hide the 'Show X entries' dropdown
        "info": false,              // Hide record info
        "dom": '<"top"f>t<"bottom"p><"clear">',  // Layout structure
        "language": {
            "search": "",           // Empty label for search
            "searchPlaceholder": "Search..."
        }
    });

    // Style adjustments
    $('.dataTables_filter input').addClass('form-control'); // Style for search input
    $('.dataTables_paginate').addClass('pagination justify-content-end'); // Align pagination right
});
