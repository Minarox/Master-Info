<template>
  <div class="popup">
    <article>
      <header>
        <h2>Créer un nouveau groupe</h2>
      </header>

      <section>
        <form @submit.prevent="formGroup">
          <label for="name">Nom du groupe :</label>
          <input type="text" name="name" id="name" v-model="name" required>
          <div>
            <button type="submit">Valider</button>
            <button type="button" class="button btn-back" @click="$emit('component', {name: ''})">Retour</button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "../../assets/js/api";

export default {
  name: "AddGroup",
  data() {
    return {
      name: ''
    }
  },
  mounted() {
    this.addEvents('', document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents('', document.getElementsByClassName("popup")[0]);
  },
  methods: {
    formGroup() {
      if (this.name) {
        API.addGroup(this.name).then(() => {
          this.$router.go(0);
        });
      }
    }
  }
};
</script>

<style scoped>
.popup {
  animation: fade-in .4s alternate;
}

.popup article section form input {
  margin-bottom: 24px;
}

.popup article section form div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  gap: 12px 40px;
}

.popup article section form div button {
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
  .popup article section form div {
    flex-flow: column nowrap;
    justify-content: center;
  }

  .popup article section form div button {
    width: 100%;
  }
}
</style>
