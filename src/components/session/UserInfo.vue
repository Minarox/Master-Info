<template>
  <div class="popup">
    <article>
      <header>
        <h2>Votre profil</h2>
      </header>

      <section>
        <form>
          <label for="email">Adresse email :</label>
          <input type="email" name="email" id="email" v-model="email" disabled />
          <label for="first_name">Prénom :</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" disabled />
          <label for="last_name">Nom :</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" disabled />
          <div>
            <button
              type="button"
              class="button"
              @click="$emit('component', { name: 'EditUserInfo' })"
            >
              Modifier
            </button>
            <button
                type="button"
                class="button btn-warning"
                @click="$emit('component', { name: 'EditPassword' })"
            >
              Changer le mot de passe
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              Fermer
            </button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
export default {
  name: "UserInfo",
  props: ["user"],
  data() {
    return {
      email: this.user["email"],
      first_name: this.user["first_name"],
      last_name: this.user["last_name"]
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  }
};
</script>

<style scoped>
.popup {
  animation: fade-in 0.4s alternate;
}

.popup article section form input,
.popup article section form select {
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
