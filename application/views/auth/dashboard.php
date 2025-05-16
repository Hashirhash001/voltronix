<?php
// From $deals (aggregated counts)
$total_deals = 0;
$deals_status_count = [
    'Site Visit' => 0,
    'Proposal' => 0,
    'Close to Won' => 0,
    'Closed Lost/Omitted' => 0
];

foreach ($deals as $deal) {
    $total_deals += $deal['total'];
    switch ($deal['status']) {
        case 'Site Visit':
            $deals_status_count['Site Visit'] += $deal['total'];
            break;
        case 'Proposal':
            $deals_status_count['Proposal'] += $deal['total'];
            break;
        case 'Close to Won':
            $deals_status_count['Close to Won'] += $deal['total'];
            break;
        case 'Close to Lost':
        case 'Omitted':
            $deals_status_count['Closed Lost/Omitted'] += $deal['total'];
            break;
    }
}

// From $analytics (initial data, will be replaced by AJAX if needed)
$deals_stage_count = [
    'Enquiry' => 0,
    'Qualification' => 0,
    'Site Visit' => 0,
    'Proposal' => 0,
    'Close to Won' => 0,
    'Closed Lost/Omitted' => 0
];
$deal_progression = [];

foreach ($analytics as $deal) {
    if (!empty($deal['enq_number'])) {
        $deals_stage_count['Enquiry'] += 1;
    }
    if (!empty($deal['qual_deal_number'])) {
        $deals_stage_count['Qualification'] += 1;
    }
    if (!empty($deal['site_deal_number'])) {
        $deals_stage_count['Site Visit'] += 1;
    }
    if (!empty($deal['quote_deal_number'])) {
        $deals_stage_count['Proposal'] += 1;
    }
    if (!empty($deal['job_deal_number'])) {
        $deals_stage_count['Close to Won'] += 1;
    }
    if (!empty($deal['lost_deal_number'])) {
        $deals_stage_count['Closed Lost/Omitted'] += 1;
    }

    $deal_progression[] = [
        'deal_name' => $deal['deal_name'],
        'enq_number' => $deal['enq_number'] ?? '',
        'enq_deal_date' => $deal['enq_deal_date'] ?? '',
        'qual_deal_number' => $deal['qual_deal_number'] ?? '',
        'qual_deal_date' => $deal['qual_deal_date'] ?? '',
        'site_deal_number' => $deal['site_deal_number'] ?? '',
        'site_deal_date' => $deal['site_deal_date'] ?? '',
        'quote_deal_number' => $deal['quote_deal_number'] ?? '',
        'quote_deal_date' => $deal['quote_deal_date'] ?? '',
        'job_deal_number' => $deal['job_deal_number'] ?? '',
        'job_deal_date' => $deal['job_deal_date'] ?? '',
        'lost_deal_number' => $deal['lost_deal_number'] ?? '',
        'lost_deal_date' => $deal['lost_deal_date'] ?? '',
        'current_status' => $deal['status']
    ];
}
?>

<!-- Main Content -->
<div class="content flex-grow-1" id="mainContent">
    <div class="container p-2" style="padding-top: 100px !important;">
        <!-- Cards for deal counts -->
        <div class="row">
            <div class="col-md-3">
                <div class="card-analytics text-white p-3 bg-secondary">
                    <div class="d-flex align-items-flex-start">
                        <i class="bi bi-list-task card-icon me-3"></i>
                        <div>
                            <h5>Total Jobs</h5>
                            <h3 id="total-jobs-count"><?= $total_deals; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-analytics text-white p-3" style="background-color: #1a99a0;">
                    <div class="d-flex align-items-flex-start">
                        <i class="bi bi-geo-alt card-icon me-3"></i>
                        <div>
                            <h5>Jobs in Site Visit</h5>
                            <h3 id="site-visit-count"><?= $deals_status_count['Site Visit']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-analytics text-white p-3" style="background-color: #f7c948;">
                    <div class="d-flex align-items-flex-start">
                        <i class="bi bi-file-earmark-text card-icon me-3"></i>
                        <div>
                            <h5>Jobs in Proposal</h5>
                            <h3 id="proposal-count"><?= $deals_status_count['Proposal']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-analytics text-white p-3" style="background-color: #1e7b3c;">
                    <div class="d-flex align-items-flex-start">
                        <i class="bi bi-check2-circle card-icon me-3"></i>
                        <div>
                            <h5>Closed Won Jobs</h5>
                            <h3 id="close-to-won-count"><?= $deals_status_count['Close to Won']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Chart -->
        <div class="row mt-4">
            <div class="col-md-12">
                <form id="dateFilterForm" method="POST" action="" class="d-flex align-items-end gap-2 justify-content-center">
                    <div>
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" value="" class="form-control" style="width: unset;">
                    </div>
                    <div>
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" value="" class="form-control" style="width: unset;">
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Filter</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary mt-2">Reset</button>
                </form>
            </div>
            <div class="col-md-6">
                <figure class="highcharts-figure">
                    <div id="container" class="chart-container"></div>
                </figure>
            </div>
            <div class="col-md-6">
                <figure class="highcharts-figure">
                    <div id="container-drilldown" class="chart-container"></div>
                </figure>
            </div>

            <!-- Progression Table (using $analytics) -->
            <div class="col-md-12 mt-4">
                <div class="card" style="border: none;">
                    <div class="card-header" style="background: linear-gradient(90deg, #e5ebf1, #e9ecef); display: flex; align-items: center; justify-content: flex-start; padding: 1rem; gap: 10px;">
                        <h5 class="text-dark" style="margin-bottom: 0; font-weight: 600;">Deal Progression</h5>
                        <div class="progress-icon">
                            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #000;"></i>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Deal Name</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Enquiry</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Qualification</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Site Visit</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Proposal</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Won</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Lost</th>
                                        <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Current Status</th>
                                    </tr>
                                </thead>
                                <tbody id="deal-progression-body">
                                    <!-- Initial data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="pagination-container d-flex justify-content-center mt-4">
                <ul class="pagination pagination-modern" id="pagination">
                    <!-- Pagination Links Will Be Injected Here -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle logout button click
    $('#logoutButton').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, log out!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#logoutButton').prop('disabled', true).text('Logging out...');
                $.ajax({
                    url: '<?= base_url('web/Login/logout') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged Out',
                                text: 'You have been successfully logged out.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '<?= base_url('web/Login/index') ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Logout Failed',
                                text: response.message || 'An unexpected error occurred.',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#logoutButton').prop('disabled', false).text('Logout');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while logging out. Please try again.',
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    let dealsStatusCount = {};
    function updateCardCounts(dealsStatusCount) {
        const totalJobs = (dealsStatusCount['Site Visit'] || 0) +
                         (dealsStatusCount['Proposal'] || 0) +
                         (dealsStatusCount['Close to Won'] || 0) +
                         (dealsStatusCount['Closed Lost/Omitted'] || 0);
        $('#total-jobs-count').text(totalJobs);
        $('#site-visit-count').text(dealsStatusCount['Site Visit'] || 0);
        $('#proposal-count').text(dealsStatusCount['Proposal'] || 0);
        $('#close-to-won-count').text(dealsStatusCount['Close to Won'] || 0);
    }
    function loadUnfilteredDealStatus() {
        $.ajax({
            url: '<?= base_url('web/Analytics/get_filtered_deal_status') ?>',
            type: 'POST',
            data: { start_date: '', end_date: '' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    dealsStatusCount = response.deals_status_count;
                    updateCardCounts(dealsStatusCount);
                    Highcharts.chart('container', {
                        chart: { type: 'pie' },
                        title: { text: 'Jobs Status Distribution' },
                        tooltip: { valueSuffix: '' },
                        credits: { enabled: false },
                        plotOptions: {
                            series: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: [{
                                    enabled: true,
                                    distance: 20
                                }, {
                                    enabled: true,
                                    distance: -40,
                                    format: '{point.percentage:.1f}%',
                                    style: { fontSize: '1.2em', textOutline: 'none', opacity: 0.7 },
                                    filter: { operator: '>', property: 'percentage', value: 10 }
                                }]
                            }
                        },
                        series: [{
                            name: 'Deals',
                            colorByPoint: true,
                            data: [
                                { name: 'Site Visit', y: dealsStatusCount['Site Visit'] || 0, color: '#1a99a0' },
                                { name: 'Proposal', y: dealsStatusCount['Proposal'] || 0, color: '#f7c948' },
                                { name: 'Close to Won', y: dealsStatusCount['Close to Won'] || 0, color: '#1e7b3c' },
                                { name: 'Closed Lost/Omitted', y: dealsStatusCount['Closed Lost/Omitted'] || 0, color: '#e74c3c' }
                            ]
                        }]
                    });
                    Highcharts.chart('container-drilldown', {
                        chart: { type: 'column' },
                        title: { text: 'Job Status Distribution' },
                        xAxis: { type: 'category', categories: ['Site Visit', 'Proposal', 'Close to Won', 'Closed Lost/Omitted'] },
                        yAxis: { title: { text: 'Number of Jobs' } },
                        legend: { enabled: false },
                        credits: { enabled: false },
                        plotOptions: { series: { borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}' } } },
                        tooltip: {
                            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> jobs<br/>'
                        },
                        series: [{
                            name: 'Jobs',
                            colorByPoint: true,
                            data: [
                                { name: 'Site Visit', y: dealsStatusCount['Site Visit'] || 0, color: '#1a99a0' },
                                { name: 'Proposal', y: dealsStatusCount['Proposal'] || 0, color: '#f7c948' },
                                { name: 'Close to Won', y: dealsStatusCount['Close to Won'] || 0, color: '#1e7b3c' },
                                { name: 'Closed Lost/Omitted', y: dealsStatusCount['Closed Lost/Omitted'] || 0, color: '#e74c3c' }
                            ]
                        }]
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load initial data. Please try again.',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while loading initial data. Please try again.',
                    showConfirmButton: true
                });
            }
        });
    }
    loadUnfilteredDealStatus();
    $('#dateFilterForm').on('submit', function(e) {
        e.preventDefault();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        $.ajax({
            url: '<?= base_url('web/Analytics/get_filtered_deal_status') ?>',
            type: 'POST',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    dealsStatusCount = response.deals_status_count;
                    updateCardCounts(dealsStatusCount);
                    Highcharts.chart('container', {
                        // Same chart configuration as above
                        chart: { type: 'pie' },
                        title: { text: 'Jobs Status Distribution' },
                        tooltip: { valueSuffix: '' },
                        credits: { enabled: false },
                        plotOptions: {
                            series: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: [{
                                    enabled: true,
                                    distance: 20
                                }, {
                                    enabled: true,
                                    distance: -40,
                                    format: '{point.percentage:.1f}%',
                                    style: { fontSize: '1.2em', textOutline: 'none', opacity: 0.7 },
                                    filter: { operator: '>', property: 'percentage', value: 10 }
                                }]
                            }
                        },
                        series: [{
                            name: 'Deals',
                            colorByPoint: true,
                            data: [
                                { name: 'Site Visit', y: dealsStatusCount['Site Visit'] || 0, color: '#1a99a0' },
                                { name: 'Proposal', y: dealsStatusCount['Proposal'] || 0, color: '#f7c948' },
                                { name: 'Close to Won', y: dealsStatusCount['Close to Won'] || 0, color: '#1e7b3c' },
                                { name: 'Closed Lost/Omitted', y: dealsStatusCount['Closed Lost/Omitted'] || 0, color: '#e74c3c' }
                            ]
                        }]
                    });
                    Highcharts.chart('container-drilldown', {
                        // Same chart configuration as above
                        chart: { type: 'column' },
                        title: { text: 'Job Status Distribution' },
                        xAxis: { type: 'category', categories: ['Site Visit', 'Proposal', 'Close to Won', 'Closed Lost/Omitted'] },
                        yAxis: { title: { text: 'Number of Jobs' } },
                        legend: { enabled: false },
                        credits: { enabled: false },
                        plotOptions: { series: { borderWidth: 0, dataLabels: { enabled: true, format: '{point.y}' } } },
                        tooltip: {
                            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> jobs<br/>'
                        },
                        series: [{
                            name: 'Jobs',
                            colorByPoint: true,
                            data: [
                                { name: 'Site Visit', y: dealsStatusCount['Site Visit'] || 0, color: '#1a99a0' },
                                { name: 'Proposal', y: dealsStatusCount['Proposal'] || 0, color: '#f7c948' },
                                { name: 'Close to Won', y: dealsStatusCount['Close to Won'] || 0, color: '#1e7b3c' },
                                { name: 'Closed Lost/Omitted', y: dealsStatusCount['Closed Lost/Omitted'] || 0, color: '#e74c3c' }
                            ]
                        }]
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to filter data. Please try again.',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while filtering data. Please try again.',
                    showConfirmButton: true
                });
            }
        });
    });
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        loadUnfilteredDealStatus();
    });
});
</script>

<script>
$(document).ready(function() {
    let currentPage = 1;
    const baseUrl = '<?= base_url('web/deal/details/') ?>';
    function loadDeals(page) {
        $.ajax({
            url: '<?= base_url('web/Analytics/get-paginated-deals') ?>',
            type: 'POST',
            data: { page: page },
            dataType: 'json',
            success: function(response) {
                let tbody = $('#deal-progression-body');
                tbody.empty();
                if (response.deal_progression && response.deal_progression.length > 0) {
                    $.each(response.deal_progression, function(index, deal) {
                        tbody.append(`
                            <tr>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: left;">
                                    <a href="${baseUrl}${deal.id}" style="text-decoration: none !important;">
                                        ${deal.deal_name ? escapeHtml(deal.deal_name) : ''}
                                    </a>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.enq_number ? escapeHtml(deal.enq_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.enq_deal_date ? escapeHtml(formatDate(deal.enq_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.qual_deal_number ? escapeHtml(deal.qual_deal_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.qual_deal_date ? escapeHtml(formatDate(deal.qual_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.site_deal_number ? escapeHtml(deal.site_deal_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.site_deal_date ? escapeHtml(formatDate(deal.site_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.quote_deal_number ? escapeHtml(deal.quote_deal_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.quote_deal_date ? escapeHtml(formatDate(deal.quote_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.job_deal_number ? escapeHtml(deal.job_deal_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.job_deal_date ? escapeHtml(formatDate(deal.job_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.lost_deal_number ? escapeHtml(deal.lost_deal_number) : ''}<br>
                                    <small style="font-size: 0.8rem; color: #666;">
                                        ${deal.lost_deal_date ? escapeHtml(formatDate(deal.lost_deal_date)) : ''}
                                    </small>
                                </td>
                                <td style="border: none !important; padding: 0.4rem 0.4rem !important; text-align: center;">
                                    ${deal.status ? escapeHtml(deal.status) : ''}
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="8" class="text-center">No deals found.</td></tr>');
                }
                updatePagination(response.total_pages, parseInt(response.current_page));
                currentPage = parseInt(response.current_page);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error loading deals.');
            }
        });
    }
    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }
    function escapeHtml(text) {
        return $('<div/>').text(text).html();
    }
    function updatePagination(totalPages, currentPage) {
        let pagination = $('#pagination');
        pagination.empty();
        pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a>
            </li>
        `);
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        for (let i = startPage; i <= endPage; i++) {
            pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a>
            </li>
        `);
        $('.page-link').off('click').on('click', function(e) {
            e.preventDefault();
            let page = parseInt($(this).data('page'));
            if (page && page > 0 && page <= totalPages && page !== currentPage) {
                loadDeals(page);
            }
        });
    }
    loadDeals(currentPage);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/funnel.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
