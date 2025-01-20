class TableUtility {
    constructor(options) {
        const defaults = {
            tableId: null,
            rowsPerPage: 10,
            searchPlaceholder: 'Search...',
        };

        this.config = { ...defaults, ...options };
        this.table = document.getElementById(this.config.tableId);
        if (!this.table) {
            console.error("TableUtility: Table not found with ID:", this.config.tableId);
            return;
        }

        this.rowsPerPage = this.config.rowsPerPage;
        this.currentPage = 1;
        this.originalData = Array.from(this.table.querySelectorAll('tbody tr'));
        this.filteredData = this.originalData.slice();  // Create a shallow copy of the rows
        this.sortColumn = null;
        this.sortDirection = 1;

        this.createControls();
        this.setupPagination();
        this.setupSorting();
        this.renderTable();
    }

    createControls() {
        const controlsContainer = document.createElement('div');
        controlsContainer.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'mb-3');

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = this.config.searchPlaceholder;
        searchInput.classList.add('form-control');
        searchInput.style.width = '250px';
        searchInput.style.height = '40px';
        searchInput.style.border = '1px solid #ccc';
        searchInput.style.borderRadius = '20px'; // Rounded corners
        searchInput.style.padding = '0 20px';
        searchInput.style.marginLeft = '10px';
        searchInput.style.backgroundColor = '#f1f1f1'; // Light background color for contrast
        searchInput.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
        searchInput.addEventListener('keyup', (e) => this.updateSearch(e.target.value));

        // // Append title and search to titleSearchContainer
        // titleSearchContainer.appendChild(titleHeading);
        // titleSearchContainer.appendChild(searchInput);

        const rowsPerPageContainer = document.createElement('div');
        rowsPerPageContainer.classList.add('d-flex', 'align-items-center');

        const label = document.createElement('label');
        label.textContent = 'Show ';
        label.classList.add('me-2');

        const rowsPerPageSelector = document.createElement('select');
        rowsPerPageSelector.classList.add('form-select', 'me-2');
        rowsPerPageSelector.style.width = 'auto';
        [1, 5, 10, 20, 50, -1].forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value === -1 ? 'All' : value;
            rowsPerPageSelector.appendChild(option);
        });
        rowsPerPageSelector.value = this.rowsPerPage;
        rowsPerPageSelector.addEventListener('change', (e) => {
            this.rowsPerPage = parseInt(e.target.value);
            if (this.rowsPerPage === -1) {
                this.rowsPerPage = this.filteredData.length; // Set to total count when "All" is selected
            }
            this.currentPage = 1;
            this.renderTable();
        });

        rowsPerPageContainer.appendChild(label);
        rowsPerPageContainer.appendChild(rowsPerPageSelector);
        rowsPerPageContainer.appendChild(document.createTextNode(' entries'));

        controlsContainer.appendChild(rowsPerPageContainer);
        controlsContainer.appendChild(searchInput);

        this.table.parentNode.insertBefore(controlsContainer, this.table);
    }

    renderTable() {
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        const visibleRows = this.filteredData.slice(start, end);

        const tbody = this.table.querySelector('tbody');
        tbody.innerHTML = '';  // Clear existing rows
        visibleRows.forEach(row => tbody.appendChild(row));  // Append sorted and paginated rows

        this.updatePaginationControls();
    }

    setupPagination() {
        const paginationControls = document.createElement('div');
        paginationControls.id = `${this.config.tableId}-pagination-controls`;
        paginationControls.classList.add('pagination-controls', 'd-flex', 'justify-content-end', 'mt-3');
        this.table.parentNode.insertBefore(paginationControls, this.table.nextSibling);
    }

    updatePaginationControls() {
        const paginationControls = document.getElementById(`${this.config.tableId}-pagination-controls`);
        paginationControls.innerHTML = '';

        const totalPages = Math.ceil(this.filteredData.length / this.rowsPerPage);
        const createPageButton = (text, isActive, onClick) => {
            const button = document.createElement('button');
            button.textContent = text;
            button.className = `btn btn-sm ${isActive ? 'btn-primary' : 'btn-secondary'} mx-1`;
            button.addEventListener('click', onClick);
            return button;
        };

        paginationControls.appendChild(createPageButton("Previous", false, () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.renderTable();
            }
        }));

        let startPage = Math.max(1, this.currentPage - 1);
        let endPage = Math.min(totalPages, this.currentPage + 1);

        if (this.currentPage === 1) endPage = Math.min(totalPages, 3);
        if (this.currentPage === totalPages) startPage = Math.max(1, totalPages - 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationControls.appendChild(createPageButton(i, i === this.currentPage, () => {
                this.currentPage = i;
                this.renderTable();
            }));
        }

        paginationControls.appendChild(createPageButton("Next", false, () => {
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.renderTable();
            }
        }));
    }

    setupSorting() {
        const headers = this.table.querySelectorAll('thead th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                if (this.sortColumn === index) {
                    this.sortDirection *= -1;  // Toggle direction
                } else {
                    this.sortDirection = 1;
                    this.sortColumn = index;
                }
                this.sortData(index);
                this.renderTable();
            });
        });
    }

    sortData(columnIndex) {
        this.filteredData.sort((a, b) => {
            const aText = a.children[columnIndex].innerText.trim();
            const bText = b.children[columnIndex].innerText.trim();

            const aValue = isNaN(aText) ? aText.toLowerCase() : parseFloat(aText);
            const bValue = isNaN(bText) ? bText.toLowerCase() : parseFloat(bText);

            if (aValue < bValue) return -1 * this.sortDirection;
            if (aValue > bValue) return 1 * this.sortDirection;
            return 0;
        });
    }

    updateSearch(searchTerm) {
        this.filteredData = this.originalData.filter(row =>
            row.innerText.toLowerCase().includes(searchTerm.toLowerCase())
        );
        this.currentPage = 1;
        this.renderTable();
    }
}
