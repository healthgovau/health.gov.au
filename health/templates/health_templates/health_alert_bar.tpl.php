<div id="ajax-container">
  <div>
    <pulse-loader :loading="loading" :color="color" :size="size"></pulse-loader>
    <ul>
      <result-item v-for="(item, index) in titles" v-bind:key="item.id" v-bind:title="item.title"></result-item>
    </ul>
  </div>
</div>
