<template>
    <div class="row mt-5 ml-5 mr-5">
        <div  class="col-sm-12">
            <b-card
                img-top
                tag="article"
                class="mb-2"
            >
                <h1 class="text-center" >Find your perfect home by county</h1>
                <form role="form" method="POST" action="/county/properties" @submit="alert('submitted')">
                    <input type="hidden" name="_token" :value="csrf">
                    <b-input-group>
                        <b-form-input placeholder="e.g. 'Los Angeles', 'Santa Barbara', 'Kern, Inland'" @input="autocomplete" @focus="autocomplete" name="selectedValue" autocomplete="off" :value="this.selectedValue"></b-form-input>
                        <input name="selectedId" :value="this.selectedId" style="display: none">
                        <button id="searchButton" type="submit" class="btn btn-primary" disabled>Search</button>
                    </b-input-group>
                    <div id="myInputautocomplete-list" class="autocomplete-items">
                        <div v-for="(result, id) in autocompleteResults" v-on:click="select($event)"  :id="id">
                            {{ result.CountyName }}, {{ result.AdminRegion }}
                        </div>
                    </div>
                </form>
            </b-card>
        </div>
    </div>
</template>

<script>
export default {
name: "ByCounties",
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        autocompleteResults: [],
        autocompleteFetched: false,
        searchInput: '',
        selectedValue: '',
        selectedId: ''
    }),
    methods: {
        autocomplete: function (input) {
            document.getElementById('searchButton').setAttribute('disabled', 'true');

            var x = document.getElementById("myInputautocomplete-list");
            x.style.display = "block";
            if (!(typeof input === 'string' || input instanceof String)) {

                input = this.searchInput ? this.searchInput : '*';
            } else {
                this.searchInput = input;
            }
            fetch("/api/counties/by-name", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                    searchQuery: input
                })
            })
                .then((response) => response.json())
                .then((data) => {
                    this.autocompleteResults = JSON.parse(JSON.stringify(data));
                    this.autocompleteFetched = true;
                })
        },
        hideBox: function () {
            var x = document.getElementById("myInputautocomplete-list");
            x.style.display = "none";
        },
        select: function(event) {
            this.selectedId = event.currentTarget.id;
            this.selectedValue = event.currentTarget.innerHTML.trim();
            var x = document.getElementById("myInputautocomplete-list");
            x.style.display = "none";
            document.getElementById('searchButton').removeAttribute('disabled');
        }
    }
}
</script>

<style scoped>
.card {
    height: 85vh;
    background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("/storage/icons/houses-background.jpg");
}

h1 {
    font-size: 90px;
    color: white;
    margin-top:13%;
}

input {
    font-size: 30px;
}

.autocomplete-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
}

.autocomplete-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff;
    border-bottom: 1px solid #d4d4d4;
}
.autocomplete-items div:hover {
    /*when hovering an item:*/
    background-color: #e9e9e9;
}

form {
    position: relative;
}
</style>
