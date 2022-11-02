<template>
    <b-container fluid="xxl">
        <!-----
        <div class="row mt-5">
            <form role="form" method="POST" action="/properties/search" @submit="alert('submitted')">
                <input type="hidden" name="_token" :value="csrf">
                <b-input-group>
                    <b-form-input placeholder="Find your new home now" @input="autocomplete" @focus="autocomplete" autocomplete="off"></b-form-input>
                    <b-input-group-append>
                        <b-button variant="secondary" type="primary"><font-awesome-icon icon="fa-magnifying-glass" /></b-button>
                    </b-input-group-append>
                </b-input-group>
            </form>
        </div>
        <div class="row" v-if="autocompleteFetched">
            <ul>
                <li v-for="result in autocompleteResults">
                    {{ result }}
                </li>
            </ul>
        </div>
        ----->
        <div class="row mt-5">
            <div  class="col-sm-4">
                <b-card
                    img-src="https://www.worldatlas.com/r/w1200/upload/5e/6d/b3/shutterstock-222278563.jpg"
                    img-top
                    tag="article"
                    class="mb-2"
                >
                    <b-card-text>
                    </b-card-text>
                    <b-button href="#" variant="primary"><router-link :to="{ name: 'by-counties' }" class="navbar-brand mx-3">Search By Counties</router-link>
                    </b-button>
                </b-card>
            </div>
            <div  class="col-sm-4">
                <b-card
                    img-src="https://i.ytimg.com/vi/tfyFElO_pcA/maxresdefault.jpg"
                    img-top
                    tag="article"
                    class="mb-2"
                >
                    <b-card-text>
                    </b-card-text>
                    <b-button href="#" variant="primary"><router-link :to="{ name: 'properties-index' }" class="navbar-brand mx-3">Search By Drawing</router-link>
                    </b-button>
                </b-card>
            </div>
            <div  class="col-sm-4">
                <b-card
                    img-src="https://cdn.dribbble.com/users/250541/screenshots/10859623/icons_01_4x.jpg"
                    img-top
                    tag="article"
                    class="mb-2"
                >
                    <b-card-text>
                    </b-card-text>
                    <b-button href="#" variant="primary"><router-link :to="{ name: 'properties-index' }" class="navbar-brand mx-3">Search By Amenities</router-link>
                    </b-button>
                </b-card>
            </div>
        </div>
    </b-container>
</template>

<script>
export default {
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        autocompleteResults: [],
        autocompleteFetched: false
    }),
    methods: {
        search: function (input) {
            let results = ["We couldn't find any results"];
            return new Promise((resolve) => {
                if (input.length < 3) {
                    return resolve([])
                }

                fetch("/api/properties/autocomplete", {
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
                        resolve(data)
                    })
            })
        },
        autocomplete: function (input) {
            if (!(typeof input === 'string' || input instanceof String))
                input = '';
            fetch("/api/properties/autocomplete", {
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
                    console.log(data);
                })
        },
    }
}
</script>
<style scoped>
input, input:focus, input:focus-visible, input:focus-within {
}
.card-img-top {
    height: 400px;
}
</style>
