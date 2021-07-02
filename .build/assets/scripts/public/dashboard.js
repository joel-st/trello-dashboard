export function loadOverview() {
    const $overview = $('#tvptd-organization-overview');
    const $overviewContent = $('#tvptd-organization-overview .tvptd__widget-content');

    if($overview) {

        $.ajax({
            type: "GET",
            url: tvpTdVars.ajaxUrl,
            data: {
                action: 'tvptd-public-ajax-get-organization-overview',
            },
            success: function (response) {
                const parsedResponse = JSON.parse(response);
                $overviewContent.html(parsedResponse.html);
                $overviewContent.removeClass('tvptd__widget-content--loading');
            },
            error: function (error) {
                $overviewContent.html('<section class="tvptd__widget-section">Oups, something went wrong wile loading the overview.</section>');
                $overviewContent.removeClass('tvptd__widget-content--loading');
            }
        });
    }
}

export function loadStatistics(timeRange = false) {
    const $statistics = $('#tvptd-organization-statistics');
    const $statisticsContent = $('#tvptd-organization-statistics .tvptd__widget-content');
    const $timeRange = $('#tvptd-organization-statistics-timerange');

    $timeRange.on('change', (event) => {
        loadStatistics(event.target.value);
    });

    if(!timeRange) {
        timeRange = $timeRange.val();
    }

    if($statistics) {
        $statisticsContent.addClass('tvptd__widget-content--loading');
        $statisticsContent.html('<div class="tvptd__spinner spinner"></div>');

        $.ajax({
            type: "GET",
            url: tvpTdVars.ajaxUrl,
            data: {
                action: 'tvptd-public-ajax-get-organization-statistics',
                timeRange: timeRange,
            },
            success: function (response) {
                const parsedResponse = JSON.parse(response);
                $statisticsContent.html(parsedResponse.html);
                $statisticsContent.removeClass('tvptd__widget-content--loading');
            },
            error: function (error) {
                $statisticsContent.html('<section class="tvptd__widget-section">Oups, something went wrong wile loading the statistics.</section>');
                $statisticsContent.removeClass('tvptd__widget-content--loading');
            }
        });
    }
}

export default { loadOverview, loadStatistics }