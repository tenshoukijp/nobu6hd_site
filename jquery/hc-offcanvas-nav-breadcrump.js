function getPageParam() {
    // 現在のURLを取得
    const url = new URL(window.location.href);

    // URLからクエリストリングの部分を取得
    const queryString = url.search;

    // パラメータ名からパラメータ値を取得
    const urlParams = new URLSearchParams(queryString);
    const pageParam = urlParams.get('page');

    return pageParam;
}

function findPreviousULSibling(element) {
    if (!element) { return null; }

    var ancestorElement = element.closest('UL');
    if (!ancestorElement) {
        return null;
    }

    var previousUL = ancestorElement.previousElementSibling;
    if (previousUL && previousUL.tagName == 'A') {
        return previousUL;
    } else {
        return null;
    }
}

function normalizeElementText(text) {
    let norm_text = text.replace(/\n/g, ' ');
    norm_text = norm_text.replace(/&nbsp;/g, '');
    norm_text = norm_text.trim();
    return norm_text;
}

function createBreadCrumbs() {
    try {
        let pageParam = getPageParam();
        let currentElement = document.getElementById(pageParam);

        // パンくずリストを格納する配列
        let breadcrumbs = [];
        let text = normalizeElementText(currentElement.innerText);
        breadcrumbs.push(text);
        let targetItem = currentElement;
        // 最大でも8層程度でしょ
        for (let i = 0; i < 8; i++) {
            targetItem = findPreviousULSibling(targetItem);
            if (!targetItem) { break; }
            let text = normalizeElementText(targetItem.innerText);
            breadcrumbs.push(text);
        }
        breadcrumbs.reverse(); // 子供⇒親の順番で格納されているので、反対のして親⇒子供の順番にする。
        breadcrumbs.unshift("目次"); // 一番上は「目次」なのでそれを追加する。
        for (let b = 0; b < breadcrumbs.length; b++) {
            breadcrumbs[b] = "<li>" + breadcrumbs[b] + "</li>"
        }
        return breadcrumbs.join("\n"); // ぱんくず文字列化
    } catch (q) {

    }

    return "";
}

let strBreadCrumb = createBreadCrumbs();
document.getElementById('breadcrump').innerHTML = strBreadCrumb;
