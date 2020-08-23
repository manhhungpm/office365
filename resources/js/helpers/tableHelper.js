export const generateTableAction = (type, action, colorInput, iconInput, titleInput, text = '') => {
    let color = '', icon = '', title = ''

    switch
        (type) {
        case 'edit':
            color = 'primary'
            icon = 'la-edit'
            title = 'Chỉnh sửa'
            break
        case 'delete':
            color = 'danger'
            icon = 'la-trash'
            title = 'Xóa'
            break
        default :
            color = colorInput
            icon = iconInput
            title = titleInput
            break
    }

    if (text == '') {
        return '<a href="javascript:;" data-action="' + action + '" class="m-portlet__nav-link btn m-btn m-btn--hover-' + color + ' m-btn--icon m-btn--icon-only m-btn--pill table-action" title="' + title + '">\n'+
            '   <i class="la ' + icon + '"></i>\n' +
            '</a>'
    } else {
        return '<a href="javascript:;" data-action="' + action + '" class="m-portlet__nav-link btn m-btn m-btn--pill table-action" title="' + title + '" style="text-decoration: underline;">\n' +`${htmlEscapeEntities(text)}` +
            '</a>'
    }

}

export const htmlEscapeEntities = function (d) {
    return typeof d === 'string' ?
        d.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') :
        d
}

export const reloadIntelligently = (table) => {
    if (table.getAllData().length == 1) {
        table.nextPrePage();
    } else {
        table.reload()
    }
}


