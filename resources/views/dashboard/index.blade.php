@extends('layouts.app')

@section('title', 'ChatDesk')

@section('content')
<div class="body-wrapper">
    <div class="container-fluid">
        <section class="welcome">
            <div class="row">
                <div class="col-12">
                    <div class="col-lg-8">
                        <div class="card bg-primary text-white overflow-hidden shadow-none">
                            <div class="card-body position-relative z-1">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-sm-12">
                                        <!-- ★ Dynamic Greeting & Name -->
                                        <h5 class="fw-semibold mb-9 fs-5 text-white">{{ $greeting }}, {{ auth()->user()->name }}!</h5>
                                        <p class="mb-0">
                                            Check your stats and suggestions for using ChatDesk
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="real-time">
            <div class="row">
                <h3 class="fw-semibold mb-9">Real time overview</h3>
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Customers Online -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                                            <i class="ti ti-users fs-6"></i>
                                        </div>
                                        <div class="ms-3 align-self-center">
                                            <!-- ★ Dynamic Stat ID -->
                                            <h3 class="mb-0 fs-6" id="stat-online-customers">-</h3>
                                            <span class="text-muted">Customers online</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ongoing Chats -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-success">
                                            <i class="ti ti-brand-hipchat fs-6"></i>
                                        </div>
                                        <div class="ms-3 align-self-center">
                                            <!-- ★ Dynamic Stat ID -->
                                            <h3 class="mb-0 fs-6" id="stat-ongoing-chats">-</h3>
                                            <span class="text-muted">Ongoing chats</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logged In Agents -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-info">
                                            <i class="ti ti-users-group fs-6"></i>
                                        </div>
                                        <div class="ms-3 align-self-center">
                                            <!-- ★ Dynamic Stat ID -->
                                            <h3 class="mb-0 fs-6" id="stat-active-agents">-</h3>
                                            <span class="text-muted">Logged in agents</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-sec">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card w-100 position-relative overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <!-- ★ Dynamic Total Chats ID -->
                                            <h4 class="card-title mb-0" id="stat-total-chats">0</h4>
                                            <p class="card-subtitle mb-0">Chats</p>
                                        </div>

                                        <!-- 📅 Datepicker Filter -->
                                        <div class="input-group w-auto">
                                            <input type="text" class="form-control form-control-sm" id="chartDateRange" readonly
                                                style="cursor:pointer; background:white;">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="ti ti-calendar-clock"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div id="widgest-chart-3" class="ms-n2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =====================================
    // 🔹 1. Fetch Real-Time Stats Cards
    // =====================================
    fetch('{{ route("dashboard.stats") }}')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-online-customers').textContent = data.online_customers;
            document.getElementById('stat-ongoing-chats').textContent = data.ongoing_chats;
            document.getElementById('stat-active-agents').textContent = data.active_agents;
        })
        .catch(err => console.error('Stats Fetch Error:', err));


    // =====================================
    // 🔹 2. Setup Datepicker
    // =====================================
    var start = new Date();
    start.setDate(start.getDate() - 6); // show past 7 days initially
    var end = new Date();

    $('#chartDateRange')
        .datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(end.getFullYear(), end.getMonth(), 1),
            endDate: new Date(end.getFullYear(), end.getMonth() + 1, 0),
            maxViewMode: 0, // restrict to month view
            autoclose: true,
            todayHighlight: true,
        })
        .on('changeDate', function (e) {
            var selectedDate = e.date;
            var startDate = new Date(selectedDate);
            startDate.setDate(startDate.getDate() - 6);
            
            loadChartData(formatDate(startDate), formatDate(selectedDate));
            $('#chartDateRange').val(formatDate(startDate) + ' - ' + formatDate(selectedDate));
        });

    // Set default range display
    $('#chartDateRange').val(formatDate(start) + ' - ' + formatDate(end));


    // =====================================
    // 🔹 3. Chart Setup (Empty Initial State)
    // =====================================
    var options = {
        series: [
            {
                name: 'Chats',
                data: [],
            },
        ],
        chart: {
            toolbar: { show: false },
            height: 400,
            type: 'bar',
            fontFamily: 'inherit',
            foreColor: '#adb0bb',
        },
        colors: ['var(--bs-primary)'],
        plotOptions: {
            bar: {
                borderRadius: 3,
                columnWidth: '45%',
                endingShape: 'rounded',
            },
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: { yaxis: { lines: { show: true } } },
        xaxis: {
            categories: [],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { fontSize: '12px', colors: '#adb0bb' } },
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: (val) => val + ' Chats' },
        },
    };

    var chart = new ApexCharts(document.querySelector('#widgest-chart-3'), options);
    chart.render();

    // Load initial data for the default 7-day range
    loadChartData(formatDate(start), formatDate(end));


    // =====================================
    // 🔹 4. Fetch Chart Data Function
    // =====================================
    function loadChartData(startDate, endDate) {
        fetch(`{{ route("dashboard.chart") }}?start=${startDate}&end=${endDate}`)
            .then(res => res.json())
            .then(data => {
                // Calculate total chats for the header
                var totalChats = data.reduce((sum, item) => sum + item.total, 0);
                document.getElementById('stat-total-chats').textContent = totalChats.toLocaleString();

                // Update ApexCharts series and categories
                chart.updateOptions({
                    xaxis: {
                        categories: data.map((d) => d.dateLabel),
                    },
                    series: [
                        {
                            name: 'Chats',
                            data: data.map((d) => d.total),
                        },
                    ],
                });
            })
            .catch(err => console.error('Chart Fetch Error:', err));
    }

    function formatDate(date) {
        let y = date.getFullYear();
        let m = String(date.getMonth() + 1).padStart(2, '0');
        let d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

});
</script>
@endpush