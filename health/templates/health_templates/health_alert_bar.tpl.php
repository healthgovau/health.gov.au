<div id="health-alert-ajax-container">
    <div v-cloak class="alert-close-button" v-if="show === true">
        <button v-on:click="hideAlert" title="close" ><i class="fa fa-times" aria-hidden="true"></i></button>
    </div>
    <div v-cloak class="container" v-if="show === true">
        <div v-cloak class="health-alert-logo"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>Health alert</div>
        <div v-cloak class="health-alert-content">
            <ul>
                <li v-for="(item, index) in items">
                    <div class="alert-date">{{ item.updated_date | formatDate }}</div><div class="alert-title"><a :href="item.link">{{ item.title }}</a></div>
                </li>
            </ul>
        </div>
    </div>
</div>
