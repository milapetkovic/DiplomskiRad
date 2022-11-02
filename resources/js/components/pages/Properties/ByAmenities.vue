<template>
    <div class="row mt-5 ml-5 mr-5">
        <div  class="col-sm-12">
            <b-card
                img-top
                tag="article"
                class="mb-2"
            >
                <h1 class="text-center" >Find your perfect home by amenities</h1>
                <form role="form" method="POST" action="/properties/amenities">
                    <input type="hidden" name="_token" :value="csrf">
                    <div class="form-group row">
                        <label for="inputEmail1" class="col-sm-2 col-form-label" style="color: white"><b>Property Details</b></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="details" id="inputEmail1" placeholder="e.g. 'pool' 'antique'">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail2" class="col-sm-2 col-form-label" style="color: white"><b>Minimal Number Of Bathrooms</b></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="noBaths" id="inputEmail2" value="1" min="1" max="6">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label" style="color: white"><b>Minimal Number Of Bedrooms</b></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="noBeds" id="inputEmail3" value="1" min="1" max="6">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label" style="color: white"><b>School Proximity</b></label>
                        <div class="col-sm-10">
                            <select class="form-control" id="exampleFormControlSelect1" name="schoolProximity">
                                <option value="100m">100m</option>
                                <option value="200m">200m</option>
                                <option value="500m">500m</option>
                                <option value="1km" selected>1km</option>
                                <option value="2km">2km</option>
                                <option value="no">Not Important</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </b-card>
        </div>
    </div>
</template>

<script>
export default {
    name: "ByAmenities",
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        autocompleteResults: [],
        autocompleteFetched: false,
        searchInput: '',
        selectedValue: '',
        selectedId: ''
    }),
    methods: {
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
    margin-top: 10%;
}

input, label {
    font-size: 20px;
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
