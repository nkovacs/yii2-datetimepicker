$(document).on('dp.show', function(e) {
    var $el = $(e.target);
    var keys = $el.data("__DPKeys");
    if (keys) {
        $el.data("DateTimePicker").keyBinds(keys);
        $el.data("__DPKeys", null);
    }
});

$(document).on('dp.hide', function(e) {
    var $el = $(e.target);
    $el.data("__DPKeys", $el.data("DateTimePicker").options().keyBinds);
    $el.data("DateTimePicker").keyBinds({
        down: function(widget) {
            if (!widget) {
                this.show();
            }
        }
    });
});

// This only works if your grid view has the default .grid-view class.
/*$(document).on('dp.change', function(e) {
    var $el = $(e.target);
    var data = $el.closest('.grid-view').yiiGridView('data');
    if (data) {
        $el.closest('.grid-view').yiiGridView('applyFilter');
    }
});*/
