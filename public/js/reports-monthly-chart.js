/**
 * Renders the Reports monthly income/expense bar chart as plain SVG.
 * No external library, no CDN — this file is the entire dependency.
 *
 * Usage: call renderMonthlyChart(svgElementId, monthlyDataObject, labels)
 * where monthlyDataObject is { "2026-03": [{month, type, total}, ...], ... }
 * (the shape ReportController::index() already passes to the view) and
 * labels is { income: '...', expense: '...', noData: '...' } for i18n text.
 */
function renderMonthlyChart(svgElementId, monthlyData, labels) {
    const svg = document.getElementById(svgElementId);
    if (!svg) {
        console.error('renderMonthlyChart: no element with id "' + svgElementId + '"');
        return;
    }

    // Clear any previous render (e.g. if this is ever called again after a filter change).
    while (svg.firstChild) {
        svg.removeChild(svg.firstChild);
    }

    const months = Object.keys(monthlyData || {}).sort();
    const viewBox = svg.viewBox && svg.viewBox.baseVal;
    const width = viewBox && viewBox.width > 0 ? viewBox.width : 760;
    const height = viewBox && viewBox.height > 0 ? viewBox.height : 260;

    function svgEl(tag, attrs) {
        const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
        Object.keys(attrs || {}).forEach(function (key) { el.setAttribute(key, attrs[key]); });
        return el;
    }

    if (months.length === 0) {
        const empty = svgEl('text', {
            x: width / 2, y: height / 2, 'text-anchor': 'middle',
            'font-size': 13, fill: '#6c757d',
        });
        empty.textContent = (labels && labels.noData) || 'No data';
        svg.appendChild(empty);
        return;
    }

    function seriesFor(type) {
        return months.map(function (m) {
            const rows = monthlyData[m] || [];
            const row = rows.find(function (r) { return r.type === type; });
            const value = row ? Number(row.total) : 0;
            return Number.isFinite(value) && value > 0 ? value : 0;
        });
    }

    const incomeSeries = seriesFor('income');
    const expenseSeries = seriesFor('expense');

    const paddingLeft = 55;
    const paddingRight = 15;
    const paddingTop = 15;
    const paddingBottom = 30;
    const chartWidth = width - paddingLeft - paddingRight;
    const chartHeight = height - paddingTop - paddingBottom;

    // Math.max(1, ...) avoids a division by zero when every month totals 0.
    const maxValue = Math.max(1, Math.max.apply(null, incomeSeries.concat(expenseSeries)));
    const groupWidth = chartWidth / months.length;
    const barWidth = Math.min(28, groupWidth / 3);

    // Gridlines + y-axis value labels.
    const steps = 4;
    for (let i = 0; i <= steps; i++) {
        const value = (maxValue / steps) * i;
        const y = paddingTop + chartHeight - (chartHeight * (i / steps));
        svg.appendChild(svgEl('line', {
            x1: paddingLeft, y1: y, x2: width - paddingRight, y2: y,
            stroke: '#e9ecef', 'stroke-width': 1,
        }));
        const label = svgEl('text', {
            x: paddingLeft - 8, y: y + 4, 'text-anchor': 'end',
            'font-size': 10, fill: '#6c757d',
        });
        label.textContent = Math.round(value).toLocaleString();
        svg.appendChild(label);
    }

    months.forEach(function (m, i) {
        const groupX = paddingLeft + i * groupWidth;
        const incomeHeight = (incomeSeries[i] / maxValue) * chartHeight;
        const expenseHeight = (expenseSeries[i] / maxValue) * chartHeight;

        svg.appendChild(svgEl('rect', {
            x: groupX + groupWidth / 2 - barWidth - 2,
            y: paddingTop + chartHeight - incomeHeight,
            width: barWidth, height: Math.max(incomeHeight, 0),
            fill: '#198754', rx: 2,
        }));
        svg.appendChild(svgEl('rect', {
            x: groupX + groupWidth / 2 + 2,
            y: paddingTop + chartHeight - expenseHeight,
            width: barWidth, height: Math.max(expenseHeight, 0),
            fill: '#dc3545', rx: 2,
        }));

        const monthLabel = svgEl('text', {
            x: groupX + groupWidth / 2, y: height - 8,
            'text-anchor': 'middle', 'font-size': 10, fill: '#495057',
        });
        monthLabel.textContent = m;
        svg.appendChild(monthLabel);
    });
}
