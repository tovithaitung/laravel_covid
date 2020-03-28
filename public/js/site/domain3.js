$(document).ready(function () {
    $('.customcheck').show();
});


Vue.component('v-select', VueSelect.VueSelect);
var app = new Vue({
    el: '#app',
    data: {
        filter: filter,
        content: '<input type="checkbox" name="filter[]" :value="item.id" :id="item.val" :checked=filter.includes(item.id) v-model="filter">\n' +
                '<span class="checkmark"></span>',
        input: '',
        items: [
            {id: 0, val: 'All'},
            {id: 1, val: 'Seen'},
            {id: 2, val: 'Bought'},
            {id: 3, val: 'Interested'},
            {id: 4, val: 'Buy'},
            {id: 5, val: 'SEO'},
            {id: 6, val: 'PPN'},
            {id: 7, val: 'Auction'},
            {id: 8, val: 'News'}
        ],
        lists: [],
        options: ['developer.android.com'],
        domainLink: '',
        elements: [],
        active: false
    },
    methods: {
        changeStatus: function (id) {
            var value = $('#select1-' + id).val();
            var self = this;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/selected",
                type: 'GET',
                data: {
                    selected : value
                },
                complete : function() {

                },
                success: function (result) {
                    if (result) {
                        var tmp = value.split('-');
                        console.log(tmp[0]);
                        $('#row-'+tmp[0]).hide('slow');
                        $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                            $("#success-alert").slideUp(400);
                        });
                    }
                }
            });
        },
        filterDomain: function () {
            var self = this;
            self.active = !self.active;
            console.log(self.domainLink);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/filter",
                type: 'GET',
                data: {
                    domainLink : self.domainLink
                },
                success: function (result) {
                    if (result) {
                        self.elements = JSON.parse(result);
                        $('.hide-default').remove();
                        // $('#tableCore tbody').html(html);
                        // $('#tableCore tbody').append(html);

                    }
                }
            });
        },
        showDomain : function (id) {
            var popup = document.getElementById("domain-"+id);
            popup.classList.toggle("show");
        },
        closeDomain : function (id) {
            var popup = document.getElementById("domain-"+id);
            popup.classList.remove("show");
        },
        showAnchors : function (id) {
            var popup = document.getElementById("myPopup-"+id);
            popup.classList.toggle("show");
        },
        closeAnchors : function (id) {
            var popup = document.getElementById("myPopup-"+id);
            popup.classList.remove("show");
        }
    }
});