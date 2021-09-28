<template>
  <div id="survey">
    <section>
      <h1>{{ Dataset[category]['title_category'] }}</h1>
      <hr>
      <progress max="100" value="70"></progress>
    </section>
    <article>
      <h2>{{ survey['question'] }}</h2>
      <div id="anwsers">
        <a v-for="i in survey['answer'].length" :key="i" href="#" class="anwser" :id="'answer-' + i" @click.prevent="answer(i)">{{ survey['answer'][i-1] }}</a>
      </div>
    </article>
    <article id="help">
      <p>{{ survey['solution']['description'] }}</p>
      <h3>
        <svg id="warning" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-3x">
          <path fill="#ff4f4f" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class="">
          </path>
        </svg>
        Astuces / consignes de sécurité :
      </h3>
      <ul>
        <li v-for="i in survey['solution']['tips'].length" :key="i">{{ survey['solution']['tips'][i-1] }}</li>
      </ul>
      <p>
        Informations complémentaires:
        <a :href="survey['solution']['link']">{{ survey['solution']['link'] }}</a></p>
    </article>
    <a id="next" :href="next">{{ msg_btn }}</a>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: 'Survey',
  data() {
    return {
      toggle: false,
      msg_btn: ''
    }
  },
  methods: {
    answer(id) {
      if (!this.toggle) {
        let anwsers = document.getElementsByClassName('anwser')
        let button = document.getElementById('answer-' + id)
        let help = document.getElementById('help')
        let next = document.getElementById('next')
        let warning = document.getElementById('warning')
        if (this.survey['correct_answer'] !== id) {
          button.style.backgroundColor = "#ff4f4f";
          warning.style.display = "inline-block";
          document.getElementById('answer-' + this.survey['correct_answer']).style.backgroundColor = 'lightgreen';
        } else {
          button.style.backgroundColor = 'lightgreen';
        }
        help.style.display = next.style.display ='block';
        for (const anwser of anwsers) {
          anwser.style.pointerEvents = 'none';
        }
        this.toggle = true
      }
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
    },
    next() {
      let self = this;
      if (parseInt(this.question) === this.$store.state.Dataset[this.category]['survey'].length - 1) {
        self.msg_btn = "Retour à l'accueil";
        return '/';
      } else {
        self.msg_btn = "Question suivante";
        return '/survey/' + this.category + '/' + (parseInt(this.question) + 1);
      }
    }
  }
}
</script>

<style scoped>
#survey {
  padding: 0 12px;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
}

#survey section {
  max-width: 800px;
  width: 100%;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: left;
}

#survey section h1 {
  font-size: 1.8em;
}

#survey section hr {
  margin: 0 30px;
  width: 1px;
  height: 60px;
  border: none;
  border-left: 1px solid black;
}

#survey section progress {
  flex: 1 0 50%;
  height: 35px;
}

article {
  padding: 12px;
  max-width: 800px;
  height: auto;
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
  margin: 30px 12px;
  width: calc(100% - 12px * 2);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  font-size: 1.6em;
  font-weight: 600;
  text-align: center;
}

#anwsers {
  padding: 0 10px;
  display: flex;
  flex-wrap: wrap;
  gap: 0 20px;
}

#anwsers a {
  flex: 1 0 33%;
  cursor: pointer;
  margin: 10px 0;
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

#anwsers a:hover {
  background-color: #DDDDDD;
  transform: scale(1.05);
  transition: background-color .15s, transform .15s;
}

article:last-of-type p {
  margin: 0;
}

#next {
  display: none;
  background-color: #EFEFEF;
  border-radius: 8px;
  padding: 12px 40px;
  color: var(--primary-text);
  text-decoration: none;
  box-shadow: 0 1px 1px rgba(0,0,0,0.11),
  0 2px 2px rgba(0,0,0,0.11),
  0 4px 4px rgba(0,0,0,0.11),
  0 6px 8px rgba(0,0,0,0.11),
  0 8px 16px rgba(0,0,0,0.11);
  margin-bottom: 20px;
}

#warning {
  display: none;
  height: 20px;
  width: auto;
}

@media screen and (max-width: 700px) {
  #anwsers a {
    flex: 1 0 90%;
  }

  #next {
    width: calc(100% - 40px * 2);
    margin-bottom: 20px;
    text-align: center;
  }

  #survey section {
    flex-flow: column nowrap;
  }

  #survey section progress {
    flex: none;
    width: 100%;
  }

  #survey section hr {
    display: none;
  }
}
</style>
