<template>
  <article>
    <section class="content">
      <h3>Inscrivez-vous aux alertes</h3>
      <p>
        Une alerte vous sera envoyé en cas de forte consommation au sein de<br />
        votre commune ou si une coupure est prévue dans votre secteur.
      </p>
      <form @submit.prevent="newsletter">
        <input
          type="email"
          name="email"
          id="email"
          placeholder="Adresse email"
          required
          v-model="email"
        />
        <button type="submit">Valider</button>
      </form>
    </section>
  </article>
</template>

<script>
export default {
  name: "NewsletterComponent",
  data() {
    return {
      email: null,
    };
  },
  methods: {
    newsletter() {
      alert(this.email + " enregistré.");
    },
  },
};
</script>

<style scoped lang="scss">
article {
  padding: 3.5rem 0;
  background-color: var(--secondary-bg-color);
  text-align: center;

  section {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;

    h3 {
      font-size: 1.9em;
    }

    p {
      font-size: 1.1em;
      line-height: 1.5rem;
    }

    form {
      margin-top: 2rem;
      display: flex;
      flex-flow: row nowrap;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;

      input,
      select {
        width: 400px;
        padding: 0.6rem;
        border-radius: 8px;
        outline: none;
        border: none;
        font-size: 1.1em;
      }

      button {
        transition: all 250ms;
        padding: 0.5rem 2rem;
        border-radius: 8px;
        outline: none;
        border: none;
        font-size: 1.2em;
        background-color: var(--green-touch);
        font-weight: bold;
        cursor: pointer;
        color: var(--primary-bg-color);

        &:disabled {
          color: rgba(9, 8, 23, 0.5);
        }

        &:enabled:hover {
          transform: scale(1.05);
        }
      }
    }
  }
}
</style>
