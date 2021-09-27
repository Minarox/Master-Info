<template>
  <div id="form">
    <h1>{{ Dataset[category]['title_category'] }}</h1>
    <article>
      <h2>{{ survey['question'] }}</h2>
      <div id="responses">
        <a v-for="i in survey['answer'].length" :key="i" href="#" class="response" @click.prevent>{{ survey['answer'][i-1] }}</a>
      </div>
    </article>
    <article id="help">
      <p>{{ survey['solution']['description'] }}</p>
      <h3>Astuces / consignes de sécurité :</h3>
      <ul>
        <li v-for="i in survey['solution']['tips'].length" :key="i">{{ survey['solution']['tips'][i-1] }}</li>
      </ul>
      <p>Informations complémentaires: <a :href="survey['solution']['link']">{{ survey['solution']['link'] }}</a></p>
    </article>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: 'Survey',
  mounted() {
    let buttons = document.getElementsByClassName('response')
    let help = document.getElementById('help')
    let toggle = true
    for (const button of buttons) {
      button.addEventListener('click', function () {
        if (toggle) {
          button.style.backgroundColor = 'lightgreen';
          help.style.display = 'block';
          toggle = false;
        }
      })
    }
  },
  computed: {
    ...mapState(['Dataset']),
    category() {
      return this.$route.params.category;
    },
    question() {
      return this.$route.params.question;
    },
    survey() {
      return this.$store.state.Dataset[this.category]['survey'][this.question];
    }
  }
}
</script>

<style scoped>
#form {
  width: 100%;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
}

article {
  position: relative;
  padding: 12px;
  width: 800px;
  height: 500px;
  background-color: #EFEFEF;
  border-radius: 8px;
  box-shadow: 0 1px 1px rgba(0,0,0,0.11),
    0 2px 2px rgba(0,0,0,0.11),
    0 4px 4px rgba(0,0,0,0.11),
    0 6px 8px rgba(0,0,0,0.11),
    0 8px 16px rgba(0,0,0,0.11);
}

article:last-of-type {
  display: none;
  height: auto;
  margin: 20px 0;
}

article h2 {
  position: absolute;
  top: 150px;
  margin: 0;
  width: calc(100% - 12px * 2);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  font-size: 2.4em;
}

#responses {
  padding: 20px;
  width: calc(100% - 30px * 2);
  position: absolute;
  bottom: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 0 20px;
}

#responses a {
  flex: 1 0 33%;
  cursor: pointer;
  margin: 10px 0;
  width: 350px;
  padding: 15px;
  background-color: white;
  border: 1px solid grey;
  border-radius: 8px;
  text-align: center;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
  font-size: 1.3em;
  color: var(--primary-text);
  text-decoration: none;
}

#responses a:hover {
  background-color: #DDDDDD;
  transform: scale(1.05);
  transition: background-color .15s, transform .15s;
}

article:last-of-type p {
  margin: 0;
}
</style>
