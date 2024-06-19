// ロケーションの引数の直近の親要素のulに data-nav-active を付与する
const urlParams = new URLSearchParams(window.location.search);
const urlParamPage = urlParams.get('page');

if (urlParamPage) {
    const element = document.getElementById(urlParamPage);
    if (element) {
        const ulElement = element.closest('ul');
        if (ulElement) {
            ulElement.setAttribute('data-nav-active', '');
        }
    }
}


let $nav = $('#main-nav').hcOffcanvasNav({
    width: 400,
    levelSpacing: 25,
    insertClose: false,
    navTitle: '目次',
    levelTitles: true,
    levelTitleAsBack: false,
    labelBack: "戻る",
    disableBody: false,
    levelOpen: "overlap",
    pushContent: "#pushobj",
    closeOnClick: false,
    expanded: true
});
let Nav = $nav.data('hcOffcanvasNav');
