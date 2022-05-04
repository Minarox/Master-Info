<template>
  <div class="popup">
    <article>
      <header>
        <h2>Supprimer un utilisateur</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUser">
          <p>Voulez vous vraiment supprimer l'utilisateur {{ selected_user["first_name"] + ' ' + selected_user["last_name"] }} ?</p>
          <div>
            <button
                type="submit"
                class="button btn-warning"
            >
              Supprimer
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              Retour
            </button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "@/assets/js/api";

export default {
  name: "UserInfo",
  props: ["selected_user"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteUser: function() {
      API.deleteUser(this.selected_user["user_id"])
        .then(() => {
          this.$emit('component', { name: '' });
          this.$emit('reload');
        });
    }
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
