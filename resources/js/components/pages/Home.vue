<template>
    <b-container fluid="xxl">
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
</style>
