(this["webpackJsonpPluginpayone-payment"]=this["webpackJsonpPluginpayone-payment"]||[]).push([[10],{"2xAX":function(e,i,n){"use strict";n.r(i);i.default={template:'{% block sw_data_grid_select_item_checkbox %}\n    <sw-checkbox-field v-if="item.disabled" \n                       :value="isSelected(item.id)" \n                       @change="selectItem($event, item)" \n                       disabled="disabled" aria-disabled="disabled">\n    </sw-checkbox-field>\n\n    <sw-checkbox-field v-else\n                       :value="isSelected(item.id)"\n                       @change="selectItem($event, item)">\n    </sw-checkbox-field>\n{% endblock %}\n'}}}]);