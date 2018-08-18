export const SET_NAV = ({ commit },{oName,aName}) => {
    commit('changeNav',{
        oName,
        aName
    })
}
export const CHANGE_TITLE = ({ commit },{title}) => {
    commit('changePageTitle', {
        title
    })
}
export const CHANGE_BREAD = ({ commit },{bread_1={name:'',path:''},bread_2={name:'',path:''},bread_3={name:'',path:''}}) => {
    commit('changePageBread', {
        bread_1,bread_2,bread_3
    })
}