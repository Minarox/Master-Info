<template>
  <div class="popup">
    <article>
      <header>
        <h2>Supprimer un groupe</h2>
      </header>

      <section>
        <p>Voulez vous vraiment supprimer le groupe {{ groups[range]["name"] }} ?</p>
        <div>
          <button type="submit" class="button btn-warning" @click="deleteGroup">Valider</button>
          <button type="button" class="button btn-back" @click="$emit('component', {name: ''})">Retour</button>
        </div>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "../../assets/js/api";

export default {
  name: "DeleteGroup",
  props: [
    "groups",
    "range"
  ],
  mounted() {
    this.addEvents('', document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents('', document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteGroup() {
      API.deleteGroup(this.groups[this.range]["id"]).then(() => {
        this.$router.go(0);
      });
    }
  }
};
</script>

<style scoped>
.popup {
  animation: fade-in .4s alternate;
}

.popup article section p {
  text-align: center;
  margin-bottom: 22px;
}

.popup article section div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  gap: 20px 40px;
}

.popup article section div button {
  margin: 0;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
