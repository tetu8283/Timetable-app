// timetable.js
$(document).ready(function() {
    // Select2 のオプションの表示に背景色を適用するテンプレート関数
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var color = $(state.element).data('color');
        var $state = $(
            '<span style="background-color:' + color + '; padding: 2px 5px; display: block;">' + state.text + '</span>'
        );
        return $state;
    };

    // Select2 初期化
    $('.subject-select').select2({
        templateResult: formatState,
        templateSelection: formatState,
        escapeMarkup: function (markup) { return markup; }
    });

    // 科目選択時に、親の td の背景色を変更する処理
    $('.subject-select').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var color = selectedOption.data('color');
        $(this).closest('td').css('background-color', color ? color : '');
    });
});
