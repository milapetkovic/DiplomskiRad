<template>
    <main role="main">
        <section class="jumbotron text-center">
            <div class="container search-box">
                <h1 class="jumbotron-heading">Search for your home starts here</h1>
                <form role="form" method="POST" action="/properties/search-landing" @submit="alert('submitted')" class="search-form">
                    <input type="hidden" name="_token" :value="csrf">
                    <b-input-group>
                        <b-form-input placeholder="e.g. 'Frazier Park', 'CA', 'House with a pool'" @input="autocomplete" @focus="autocomplete" name="searchQuery" autocomplete="off"></b-form-input>
                        <input name="type" style="display: none" value="search-landing">
                        <button id="searchButton" type="submit" class="btn btn-light"><font-awesome-icon icon="fa-magnifying-glass" /></button>
                    </b-input-group>
                    <div id="myInputautocomplete-list" class="autocomplete-items">
                    </div>
                </form>
            </div>
        </section>

        <div class="album py-5 bg-light" style="background: #e9e9e9 !important; border-radius: 5px">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10">
                        <h4 style="font-weight: 400; display: inline" >
                            Available homes near you
                        </h4>
                    </div>
                    <div class="col-lg-2 text-right">
                        <h5>
                            <font-awesome-icon icon="fa-solid fa-house-chimney" />
                        </h5>
                    </div>
                </div>
                <div class="row" v-if="this.fetched" style="border-top: 1px solid lightgray; padding-top: 20px;">
                    <div class="col-md-3" v-for="property in closeProperties">
                        <div class="card mb-3 box-shadow shadow">
                            <img class="card-img-top" :data-src="property['_source']['image']" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" :src="property['_source']['image']" data-holder-rendered="true">
                            <div class="card-body">
                                <p class="card-title">{{ property['_source']['address'] }}, {{ property['_source']['city'] }}</p>
                                <div class="card-text">
                                    <h5 class="text-right" style="margin-top: 10px; margin-bottom: 10px">
                                        <font-awesome-icon icon="fa-solid fa-bath" />: {{ property['_source']['bath'] }}
                                        <font-awesome-icon icon="fa-solid fa-bed" style="margin-left: 40px;" />: {{ property['_source']['bed'] }}
                                    </h5>

                                    <div style="width: 50px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"> {{ property['_source']['description'] }}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-secondary">
                                            <router-link :to="{ path: '/properties/detail/'+ property['_source']['id'] }" style="text-decoration: none; color: white;">
                                                View More
                                            </router-link>
                                        </button>
                                    </div>
                                    <small class="text-muted">Around {{  Math.round(property['sort'][0]) }} km from you</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-10">
                        <h5 style="font-weight: 400; display: inline" >
                            Not sure what you are looking for? Try our other search options
                        </h5>
                    </div>
                    <div class="col-lg-2 text-right">
                        <h5>
                            <font-awesome-icon icon="fa fa-magnifying-glass-location" />
                        </h5>
                    </div>
                </div>
                <div class="row"style="border-top: 1px solid lightgray; padding-top: 20px;">
                    <div  class="col-sm-4">
                        <b-card
                            img-src="https://www.worldatlas.com/r/w1200/upload/5e/6d/b3/shutterstock-222278563.jpg"
                            img-top
                            tag="article"
                            class="mb-2"
                        >
                            <b-card-text>
                            </b-card-text>
                            <b-button href="#" variant="secondary"><router-link :to="{ name: 'by-counties' }" class="navbar-brand mx-3">Search By Counties</router-link>
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
                            <b-button href="#" variant="secondary"><router-link :to="{ name: 'properties-index' }" class="navbar-brand mx-3">Search By Drawing</router-link>
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
                            <b-button href="#" variant="secondary"><router-link :to="{ name: 'by-amenities' }" class="navbar-brand mx-3">Search By Amenities</router-link>
                            </b-button>
                        </b-card>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
export default {
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        autocompleteResults: [],
        autocompleteFetched: false,
        closeProperties: [],
        fetched: false
    }),
    created() {
        var x = document.getElementById("search");
        if(x) x.style.display = "none";
        this.closePropertiesFetch();
    },
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
                })
        },
        closePropertiesFetch: function () {
            fetch("/api/properties/close", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.closeProperties = data;
                    this.fetched = true;
                })
                .catch(error => { console.log(error); });
        }
    }
}
</script>
<style scoped>
input, input:focus, input:focus-visible, input:focus-within {
}
.card-img-top {
    height: 400px;
}
.search-box {
    border: 1px solid gray;
    border-radius: 10px;
    padding-top: 20px;
    background: #808080e0;
    padding-bottom: 30px;
}
.jumbotron-heading {
    color: white;
}
.jumbotron {
    margin-top: 15px;
    background: url("/storage/icons/houses_home_page.png");
}
.form-control {
    border: 1px solid white;
}
.btn-light {
    background: white;
    border: none;
}
.search-form {
    width: 60%;
    margin-left: 20%;
}
</style>
