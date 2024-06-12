var ctx = document.getElementById('attendanceChart').getContext('2d');
var attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Yesterday", "Today"], // Example labels
        datasets: [{
            label: 'Present Today per Day',
            data: [50, 75], // Example data
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
