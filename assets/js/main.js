var Table;
Table = {
    tableId: '#jsGrid',
    tableFields: [
        {name: "name", title: "Item name", type: "text", width: 150},
        {name: "count", title: "Item count", type: "number", width: 50, filtering: false},
        {type: "control"}
    ],
    tableSettings: {
        height: "80%",
        width: "100%",
        filtering: true,
        inserting: true,
        editing: true,
        sorting: true,
        paging: false,
        autoload: true,
        deleteConfirm: "Do you really want to delete item?"
    },
    url: '/table/',

    /**
     * Instance initialization
     */
    init: function () {
        this.initialTableLoad();
        this.doLongPolling();
    },

    /**
     * Table initial loading
     */
    initialTableLoad: function () {
        this.tableSettings.controller = {
            loadData: this.loadData,
            insertItem: this.insertItem,
            updateItem: this.updateItem,
            deleteItem: this.deleteItem
        };
        this.tableSettings.fields = this.tableFields;
        jQuery(this.tableId).jsGrid(this.tableSettings);
    },

    /**
     * Do data load
     * @param filter
     * @returns {*}
     */
    loadData: function (filter) {
        return Table.changeHandler(filter, 'GET', 'load');
    },

    /**
     * Process insert item action
     * @param item
     * @returns {*}
     */
    insertItem: function (item) {
        return Table.changeHandler(item, 'POST', 'insert');
    },

    /**
     * Process update item action
     * @param item
     * @returns {*}
     */
    updateItem: function (item) {
        return Table.changeHandler(item, 'POST', 'update');
    },

    /**
     * Process delete item action
     * @param item
     * @returns {*}
     */
    deleteItem: function (item) {
        return Table.changeHandler(item, 'POST', 'delete')
    },

    /**
     * Handle load/insert/delete/update actions
     * @param input
     * @param requestType
     * @param actionType
     * @returns {*}
     */
    changeHandler: function (input, requestType, actionType) {
        var url = this.url + '?action=' + actionType;
        return jQuery.ajax({
            type: requestType,
            url: url,
            data: input
        });
    },

    /**
     * Initiate long-polling flow
     */
    doLongPolling: function (tableChecksum) {
        var data = {
            action: 'long-polling',
            tableChecksum: tableChecksum
        };
        jQuery.ajax({
            type: 'GET',
            url: this.url,
            data: data,
            dataType: 'json',
            context: this,
            success: function (response) {
                var tableChecksum = response.tableChecksum;

                jQuery(this.tableId).jsGrid("loadData");

                this.doLongPolling(tableChecksum);
            }
        });
    }
};

jQuery(document).ready(function () {
    Table.init();
});