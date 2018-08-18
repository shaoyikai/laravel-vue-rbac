export const changeNav = (state, {oName,aName}) => {

    let arr = [];
    arr.push(oName)
    state.open_name = arr
    state.active_name = aName
}

export const changePageTitle = (state, {title}) => {
    state.page_title = title
}

export const changePageBread = (state, {bread_1,bread_2,bread_3}) => {
    state.bread_1 = bread_1
    state.bread_2 = bread_2
    state.bread_3 = bread_3
}