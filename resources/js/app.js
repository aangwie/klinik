import './bootstrap';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css?url';
import Swal from 'sweetalert2';

// DataTables Indonesian Language
const indonesianLang = {
    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
    "sProcessing": "Sedang memproses...",
    "sLengthMenu": "Tampilkan _MENU_ data",
    "sZeroRecords": "Tidak ditemukan data yang sesuai",
    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
    "sInfoFiltered": "(disaring dari _MAX_ data keseluruhan)",
    "sSearch": "Cari:",
    "oPaginate": {
        "sFirst": "<<",
        "sPrevious": "<",
        "sNext": ">",
        "sLast": ">>"
    }
};

// Custom styling to align search right and pagination right
const style = document.createElement('style');
style.textContent = `
    div.dt-container .dt-layout-row.dt-layout-table {
        overflow-x: auto;
    }
    div.dt-container .dt-layout-cell {
        padding: 0.5rem 0;
    }
    div.dt-container .dt-search {
        text-align: right;
        float: right;
    }
    div.dt-container .dt-search label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }
    div.dt-container .dt-search input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        font-size: 0.875rem;
        width: 200px;
    }
    div.dt-container .dt-search input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    div.dt-container .dt-length {
        float: left;
        text-align: left;
    }
    div.dt-container .dt-length label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    div.dt-container .dt-length select {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        font-size: 0.875rem;
        background: white;
    }
    div.dt-container .dt-length select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    div.dt-container .dt-paging {
        text-align: right;
        float: right;
    }
    div.dt-container .dt-paging nav {
        display: inline-flex;
        gap: 0.25rem;
    }
    div.dt-container .dt-paging button {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: white;
        font-size: 0.875rem;
        cursor: pointer;
        color: #374151;
        transition: all 0.15s;
    }
    div.dt-container .dt-paging button:hover:not(.disabled) {
        background: #f3f4f6;
    }
    div.dt-container .dt-paging button.current {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    div.dt-container .dt-paging button.disabled {
        opacity: 0.4;
        cursor: default;
    }
    div.dt-container .dt-info {
        float: left;
        font-size: 0.875rem;
        color: #6b7280;
        padding-top: 0.5rem;
    }
    div.dt-container {
        padding: 0 !important;
    }
    div.dt-container .dt-layout-row {
        margin: 0 !important;
    }
    div.dt-container .dt-layout-row:first-child {
        padding-bottom: 0.75rem;
    }
    div.dt-container .dt-layout-row:last-child {
        padding-top: 0.75rem;
    }
    div.dt-container table.dataTable {
        margin: 0 !important;
        width: 100% !important;
    }

    /* Tom Select Theme - Light Green/Emerald */
    .ts-wrapper-green .ts-control {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
        min-height: 42px !important;
        box-shadow: none !important;
    }
    .ts-wrapper-green .ts-control:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }
    .ts-wrapper-green .ts-control .item {
        background: #d1fae5 !important;
        color: #065f46 !important;
        border: 1px solid #a7f3d0 !important;
        border-radius: 0.375rem !important;
        padding: 0.125rem 0.5rem !important;
        font-size: 0.8rem !important;
    }
    .ts-wrapper-green .ts-control .item .remove {
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
    }
    .ts-wrapper-green .ts-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
        font-size: 0.875rem !important;
    }
    .ts-wrapper-green .ts-dropdown .option.active {
        background: #d1fae5 !important;
        color: #065f46 !important;
    }
    .ts-wrapper-green .ts-dropdown .option:hover {
        background: #f0fdf4 !important;
    }

    /* SweetAlert2 Custom Theme */
    .swal2-popup {
        border-radius: 1rem !important;
        padding: 2rem !important;
    }
    .swal2-title {
        font-size: 1.25rem !important;
        color: #1f2937 !important;
    }
    .swal2-confirm.btn-reset {
        background-color: #ef4444 !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-weight: 600 !important;
    }
    .swal2-confirm.btn-reset:hover {
        background-color: #dc2626 !important;
    }
    .swal2-cancel {
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-weight: 600 !important;
    }
`;
document.head.appendChild(style);

// Initialize Tom Select for enhanced multi-select
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[multiple]').forEach(select => {
        new TomSelect(select, {
            plugins: ['remove_button'],
            maxItems: null,
        });
    });

    // Auto-initialize all tables with class 'datatable'
    const tables = document.querySelectorAll('table.datatable');
    tables.forEach(table => {
        new DataTable(table, {
            language: indonesianLang,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            order: [],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            dom: '<"dt-top-row"lf>t<"dt-bottom-row"ip>'
        });
    });
});

// Global function for SweetAlert confirmations
window.confirmReset = function() {
    const dateInput = document.getElementById('resetDate');
    if (!dateInput || !dateInput.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Tanggal',
            text: 'Silakan pilih tanggal terlebih dahulu',
            confirmButtonColor: '#10b981',
        });
        return;
    }

    const date = dateInput.value;
    const formattedDate = new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    Swal.fire({
        title: 'Reset Antrean?',
        html: `
            <p style="color: #6b7280; margin-bottom: 0.5rem;">Anda akan mereset semua antrean pada:</p>
            <p style="font-weight: 600; font-size: 1.1rem; color: #1f2937;">${formattedDate}</p>
            <p style="color: #9ca3af; font-size: 0.85rem; margin-top: 0.75rem;">
                <svg style="display:inline;width:16px;height:16px;vertical-align:middle;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Hanya antrean yang direset. Data rekam medis tetap aman.
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Reset Antrean!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            confirmButton: 'btn-reset',
        },
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const response = await fetch('/queue/reset-by-date', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ reset_date: date })
                });
                const result = await response.json();
                if (!result.success) throw new Error(result.message);
                return result;
            } catch (error) {
                Swal.showValidationMessage(`Gagal: ${error.message}`);
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.value.message,
                confirmButtonColor: '#10b981',
            }).then(() => {
                window.location.reload();
            });
        }
    });
};