<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Proyek yang Berjalan -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-blue-50 to-blue-100 dark:border-neutral-700 dark:from-blue-900 dark:to-blue-800 p-6">
                <div class="flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-blue-600 dark:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-300 mb-1">Proyek Yang Berjalan</p>
                        <h3 class="text-4xl font-bold text-blue-900 dark:text-blue-100"><?php echo e($proyekBerjalan ?? 0); ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Proyek yang Sudah Selesai -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-green-50 to-green-100 dark:border-neutral-700 dark:from-green-900 dark:to-green-800 p-6">
                <div class="flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-green-600 dark:text-green-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-300 mb-1">Proyek yang Sudah Selesai</p>
                        <h3 class="text-4xl font-bold text-green-900 dark:text-green-100"><?php echo e($proyekSelesai ?? 0); ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Proyek yang Mau Berjalan -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-br from-orange-50 to-orange-100 dark:border-neutral-700 dark:from-orange-900 dark:to-orange-800 p-6">
                <div class="flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-orange-600 dark:text-orange-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-orange-600 dark:text-orange-300 mb-1">Proyek yang Mau Berjalan</p>
                        <h3 class="text-4xl font-bold text-orange-900 dark:text-orange-100"><?php echo e($proyekMenunggu ?? 0); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grafik Batang Proyek Per Bulan -->
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6" style="min-height: 450px;">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">📊 Proyek Per Bulan</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Grafik pertumbuhan proyek dalam 6 bulan terakhir (berdasarkan tanggal pembuatan)</p>
            </div>
            <div class="w-full relative" style="height: 320px;">
                <!-- Loading State -->
                <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-neutral-800">
                    <div class="text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-500 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading grafik...</p>
                    </div>
                </div>
                <canvas id="projectChart" class="opacity-0 transition-opacity duration-300"></canvas>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script>
        // Fungsi untuk inisialisasi chart
        function initChart() {
            const canvas = document.getElementById('projectChart');
            const loading = document.getElementById('chartLoading');
            
            if (!canvas || !loading) return;
            
            const ctx = canvas.getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            const chartData = <?php echo json_encode($chartData, 15, 512) ?>;
            
            const projectChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Proyek',
                        data: chartData.data,
                        backgroundColor: isDark 
                            ? 'rgba(96, 165, 250, 0.6)' 
                            : 'rgba(59, 130, 246, 0.6)',
                        borderColor: isDark 
                            ? 'rgba(96, 165, 250, 1)' 
                            : 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: isDark 
                            ? 'rgba(96, 165, 250, 0.8)' 
                            : 'rgba(59, 130, 246, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 500 // Kurangi durasi animasi dari default 1000ms ke 500ms
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: isDark ? '#e5e7eb' : '#374151',
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1f2937' : '#ffffff',
                            titleColor: isDark ? '#f9fafb' : '#111827',
                            bodyColor: isDark ? '#e5e7eb' : '#374151',
                            borderColor: isDark ? '#374151' : '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': ' + context.parsed.y + ' proyek';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: isDark ? '#9ca3af' : '#6b7280',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: isDark ? '#374151' : '#e5e7eb',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: isDark ? '#9ca3af' : '#6b7280',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Sembunyikan loading dan tampilkan chart
            setTimeout(() => {
                loading.style.display = 'none';
                canvas.classList.remove('opacity-0');
                canvas.classList.add('opacity-100');
            }, 100);

            // Update chart saat theme berubah
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        
                        // Update colors dengan animasi lebih cepat
                        projectChart.data.datasets[0].backgroundColor = isDarkMode 
                            ? 'rgba(96, 165, 250, 0.6)' 
                            : 'rgba(59, 130, 246, 0.6)';
                        projectChart.data.datasets[0].borderColor = isDarkMode 
                            ? 'rgba(96, 165, 250, 1)' 
                            : 'rgba(59, 130, 246, 1)';
                        
                        projectChart.options.plugins.legend.labels.color = isDarkMode ? '#e5e7eb' : '#374151';
                        projectChart.options.scales.y.ticks.color = isDarkMode ? '#9ca3af' : '#6b7280';
                        projectChart.options.scales.x.ticks.color = isDarkMode ? '#9ca3af' : '#6b7280';
                        projectChart.options.scales.y.grid.color = isDarkMode ? '#374151' : '#e5e7eb';
                        
                        projectChart.update('none'); // Update tanpa animasi untuk performa lebih baik
                    }
                });
            });
            
            observer.observe(document.documentElement, {
                attributes: true
            });
        }
        
        // Jalankan saat DOM ready atau Chart.js sudah load
        if (typeof Chart !== 'undefined') {
            initChart();
        } else {
            window.addEventListener('load', initChart);
        }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH /Users/prayoga/Project Om Mame/laravel-stack-crud-kit-main/resources/views/dashboard.blade.php ENDPATH**/ ?>