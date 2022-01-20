<template>
  <div class="popup">
    <article>
      <header>
        <h2>Quitter le groupe</h2>
      </header>

      <section>
        <p>Voulez vous vraiment quitter le groupe ?</p>
        <div>
          <button type="submit" class="button btn-warning" @click="leaveGroup">Valider</button>
          <button type="button" class="button btn-back" @click="$emit('component', {name: ''})">Retour</button>
        </div>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "../../assets/js/api";

export default {
  name: "LeaveGroup",
  mounted() {
    this.addEvents('', document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents('', document.getElementsByClassName("popup")[0]);
  },
  methods: {
    leaveGroup() {
      API.leaveCurrentGroup().then(() => {
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
  gap: 12px 40px;
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

@media only screen and (max-width: 500px) {
  .popup article section div {
    flex-flow: column nowrap;
    justify-content: center;
  }

  .popup article section div button {
    width: 100%;
  }
}
</style>
