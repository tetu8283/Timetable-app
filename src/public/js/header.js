document.addEventListener('DOMContentLoaded', function() {
    // サイドバー（nav要素）とトグルボタンの取得
    const sidebar = document.getElementById('js-nav');
    const toggleButton = document.getElementById('js-toggle-sidebar');

    // トグルボタンが存在し、サイドバーが取得できた場合のみイベント登録
    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', function() {
            // 「open」クラスの付与・削除でスライドイン／アウトを切り替え
            sidebar.classList.toggle('open');

            // トグルボタンのテキストを変換
            setTimeout(() => {
                if (sidebar.classList.contains('open')) {
                    toggleButton.classList.add('open');
                } else {
                    toggleButton.classList.remove('open');
                }
            }, 100); // 0.5秒後にクラスを変更
        });
    }
});
