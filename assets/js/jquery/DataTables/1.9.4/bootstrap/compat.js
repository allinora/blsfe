/* Default class modification */
$.extend($.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
});

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": oSettings._iDisplayLength,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};

/* Bootstrap style pagination control */
$.extend($.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function (oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function (e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };
//            if (Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength) > 1) {
                $(nPaging).addClass('pagination').append(
                    '<ul>' +
                        '<li class="first disabled"><a href="#">' + '««' + '</a></li>' +
                        '<li class="prev  disabled"><a href="#">' + '«' + '</a></li>' +
                        '<li class="next  disabled"><a href="#">' + '»' + '</a></li>' +
                        '<li class="last  disabled"><a href="#">' + '»»' + '</a></li>' +
                        '</ul>'
                );
                var els = $('a', nPaging);
                $(els[0]).bind('click.DT', { action: "first" }, fnClickHandler);
                $(els[1]).bind('click.DT', { action: "previous" }, fnClickHandler);
                $(els[2]).bind('click.DT', { action: "next" }, fnClickHandler);
                $(els[3]).bind('click.DT', { action: "last" }, fnClickHandler);
//            }
        },

        "fnUpdate": function (oSettings, fnDraw) {
            var iListLength = 3;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

            if (oPaging.iTotalPages > 1) {
                if (oPaging.iTotalPages < iListLength) {
                    iStart = 1;
                    iEnd = oPaging.iTotalPages;
                }
                else if (oPaging.iPage <= iHalf) {
                    iStart = 1;
                    iEnd = iListLength;
                } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                    iStart = oPaging.iTotalPages - iListLength + 1;
                    iEnd = oPaging.iTotalPages;
                } else {
                    iStart = oPaging.iPage - iHalf + 1;
                    iEnd = iStart + iListLength - 1;
                }

                for (i = 0, iLen = an.length ; i < iLen ; i++) {
                    // Remove the middle elements
                    $('li:gt(1)', an[i]).filter(':not(.next,.last)').remove();

                    // Add the new list items and their event handlers
                    for (j = iStart ; j <= iEnd ; j++) {
                        sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                        $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                            .insertBefore($('.next,.last', an[i])[0])
                            .bind('click', function (e) {
                                e.preventDefault();
                                oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                                fnDraw(oSettings);
                            });
                    }

                    // Add / remove disabled classes from the static elements
                    if (oPaging.iPage === 0) {
                        $('.first,.prev', an[i]).addClass('disabled');
                    } else {
                        $('.first,.prev', an[i]).removeClass('disabled');
                    }

                    if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                        $('.next,.last', an[i]).addClass('disabled');
                    } else {
                        $('.next,.last', an[i]).removeClass('disabled');
                    }
                }
            }
        }
    }
});

/* Bootstrap style sorting control + form wrapper */
$.extend( $.fn.dataTableExt.oStdClasses, {
	"sSortAsc": "header headerSortDown",
	"sSortDesc": "header headerSortUp",
	"sSortable": "header",
	"sWrapper": "dataTables_wrapper form-inline"
});

/* add even more classes and overwrite some styles - use bootstrap caret as sorting icon */
$.extend($.fn.dataTable.ext.oStdClasses, {
    sSortAsc: 'sort sort-up',
    sSortDesc: 'sort sort-down',
    sSortable: 'sort',
    sSortableAsc: 'sort sort-up',
    sSortableDesc: 'sort sort-down',
    sSortableNone: 'sort sort-none'
});