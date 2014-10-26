function setErrorField(field, error)  {
    if (error) {
        $(field).parent().addClass("has-error");
    } else {
        $(field).parent().removeClass("has-error");
    }
}

function clearForm(errorBox) {
    errorBox.empty();
    $('#addIncome input').parent().removeClass('has-error');    
}

function setErrorState(errorFields, errors, errorBox) {
    errorBox.empty();
    $(errors).each( function (i, f) {errorBox.append('<div>' + f + '</div>')} );
    $(errorFields).each(function (i, f) { setErrorField(f, true); });
}

var sinceWhen = 0;
var ajaxInProgress = false;

function getIncomeUpdates() {
    if (ajaxInProgress) return;
    $.post('index.php?ctrl=income&action=getIncomeUpdates', {
            since: sinceWhen,
            months: $('[name=months]').val()},
        function (data) {
            $('#incomes_table').append(data);
            sinceWhen = findLastId();
        });
    $(document)
            .ajaxStart(function () {ajaxInProgress = true; console.log('start');})
            .ajaxComplete(function () {ajaxInProgress = false; console.log('complete');});
    
}

function getIncomeUpdatesJSON() {
    if (ajaxInProgress) return;
    $.post('/report/incomeUpdates', {
            since: sinceWhen,
            months: $('[name=months]').val()},
        function (data) {
            for (i = 0; i < data.updates.length; i++) {
                var newRow = $('<tr id="row_' + data.updates[i].id + '"></tr>');
                newRow.append($('<td>' + data.updates[i].id + '</td>'));
                newRow.append($('<td>' + data.updates[i].date + '</td>'));
                newRow.append($('<td>' + data.updates[i].amount + '</td>'));
                newRow.append($('<td>' + data.updates[i].currency + '</td>'));
                newRow.append($('<td>' + data.updates[i].usdAmount + '</td>'));
                newRow.append($('<td><button class="btn btn-danger">Delete</button></td>'));
                $('#incomes_table').append(newRow);
            }
            sinceWhen = data.maxId;
        });
    $(document)
            .ajaxStart(function () {ajaxInProgress = true; console.log('start');})
            .ajaxComplete(function () {ajaxInProgress = false; console.log('complete');});
    
}

function findLastId() {
    var table = $('#incomes_table tr');
    if (!table) {
        return -1;
    } else {
        var max = 0;
        table.each( function (i, e) {
            var el = $(e).children().first();
            if (Number(el.text()) > max) max = Number(el.text());
        });
        return max;
    }
}

$().ready(function() {
    $.getJSON('http://rate-exchange.appspot.com/currency?from=USD&to=UAH&callback=?', {}, 
    function (data) {
        console.log(data.rate);
    });
    $('#incomes_table').on('click', 'button', function(e) {
        var row = $(this).parent().parent();
        var id = $(row).children().first().text();
        if (confirm('Точно удалть ' + id + '?')) {
            $.post('index.php?ctrl=income&action=delete', {id: id},
            function (data) {
                if (data.result != -1) {
                    $(row).remove();
                }
            });
        }
    });
    sinceWhen = findLastId();
    if (sinceWhen != -1) {
        if (!sinceWhen) {
            sinceWhen = 0;
        }
        setInterval(getIncomeUpdatesJSON, 5000);
    }
    
    $('#addIncome').validate({
        highlight: function (element, cssClass) {
            setErrorField($(element), true);
        },
        errorContainer: '#messages',
        errorLabelContainer: '#messages',
        unhighlight: function (element, cssClass) {
            setErrorField($(element), false);
        }
    });
});