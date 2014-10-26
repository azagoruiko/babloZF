window.requestAnimFrame = (function(callback) {
    return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
            function (callback) {
                setTimeout(callback, 1000/60);
    };
})();

function drawBar(bar, context) {
    context.beginPath();
    context.rect(bar.x, bar.y, bar.width, bar.height);
    context.fillStyle = '#8ED6FF';
    context.fill();
    context.lineWidth = 1;
    context.strokeStyle = 'black';
    context.stroke();
}

function drawLine (line, context) {
    context.beginPath();
    context.moveTo(line.from.x, line.from.y);
    context.lineTo(line.to.x, line.to.y);
    context.stroke();
}

function drawText(text, x, y, context) {
    context.font = '14px Veranda';
    context.fillStyle = 'black';
    context.fillText(text, x, y);
}

$().ready(function(){
    var canvas = $('#graph')[0];
    var context = canvas.getContext('2d');
    
    canvas.width = $(canvas).width();
    canvas.height = $(canvas).height();
    
    $.get('/report/revenue12Months', {} ,
    function(data){
        var rev = data.revenue;
        var maxRev = 0;
        for (var i = 0; i < rev.length; i++) {
            if (maxRev < Number(rev[i].balance)) maxRev = rev[i].balance;
        }
        
        var bucksPerPixel = maxRev / 250;
        var x = 100;
        
        var measureStep = Math.round(Math.round(maxRev)/100)*100/5;
        var measureStepPixels = measureStep / bucksPerPixel;
        
        drawLine({from: {x:x-10, y: 290}, to: {x: x-10, y: 10}}, context);
        drawLine({from: {x:x-10, y: 290}, to: {x: canvas.width - x, y: 290}}, context);
        
        for (var i = 1; i<=5; i++) {
            var y = measureStepPixels * i + (maxRev - measureStep*5) / bucksPerPixel;
            drawLine({from: {y: y, x:x-10}, to: {x: x-15, y: y}}, context);
            drawText('$' + measureStep * (6-i), 30, y + 5, context);
        }
        
        var graphWidth = canvas.width - x*2 - 10;
        var barWidth = graphWidth / 12 - 10;
        
        
        for (var i = 0; i < rev.length; i++) {
            var barHeight = -1 * rev[i].balance / bucksPerPixel;
            drawBar({x: x, y: 290, width: barWidth, height: barHeight}, context);
            
            var date = new Date(Number(rev[i].year), rev[i].month, 1);
            drawText(date.getMonth() + '/' + date.getFullYear(), x+3, 280, context);
            drawText('$' + Math.floor(rev[i].balance), x + 10, canvas.height + barHeight - 20, context);
            x+=barWidth + 10;
        }
    });
});


