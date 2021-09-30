<template>
  <article>
    <section>
      <h1>{{ Dataset[category]['title_category'] }}</h1>
      <hr>
      <div id="progress_bar">
        <p>Progression : {{ (100 / Dataset[category]['survey'].length) * (parseInt(question) + 1) }}%</p>
        <div id="progress">
          <div id="progress-value"></div>
        </div>
      </div>
    </section>

    <section>
      <h2>Question {{ parseInt(question) + 1 }} :</h2>
      <p>{{ survey['question'] }}</p>
      <div id="answers">
        <a v-for="i in survey['answer'].length" :key="i" href="#" class="answer" :id="'answer-' + i" @click.prevent="answer(i)">{{ survey['answer'][i-1] }}</a>
      </div>
    </section>

    <section class="hide">
      <h3>Description</h3>
      <p>{{ survey['solution']['description'] }}</p>

      <div>
        <svg id="warning" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline&#45;&#45;fa fa-exclamation-triangle fa-w-18 fa-3x">
          <path fill="#ff4f4f" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class="">
          </path>
        </svg>
        <h3>
          Astuces / consignes de sécurité :
        </h3>
      </div>
      <ul>
        <li v-for="i in survey['solution']['tips'].length" :key="i">{{ survey['solution']['tips'][i-1] }}</li>
      </ul>

      <p>
        Informations complémentaires :
        <a :href="survey['solution']['link']">{{ survey['solution']['link'] }}</a>
      </p>
    </section>
    <section>
      <a class="hide" id="next" :href="next">{{ msg_btn }}</a>
    </section>
  </article>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: 'Survey',
  data() {
    return {
      toggle: false,
      choices: [],
      msg_btn: ''
    }
  },
  mounted() {
    document.querySelector('#progress-value').style.setProperty('--ns', (100 / this.$store.state.Dataset[this.category]['survey'].length) * parseInt(this.question) + '%')
    document.querySelector('#progress-value').style.setProperty('--ne', (100 / this.$store.state.Dataset[this.category]['survey'].length) * (parseInt(this.question) + 1) + '%')
  },
  methods: {
    answer(id) {
      if (!this.toggle) {
        let warning = document.getElementById('warning')
        let answers = document.getElementsByClassName('answer')
        let section = document.getElementsByClassName('hide')

        if (this.survey['type_answer'] === "buttons" && this.survey['correct_answer'].length <= 1) {
          this.toggle = true
          let button = document.getElementById('answer-' + id)

          if (this.survey['correct_answer'][0] !== id) {
            button.style.backgroundColor = "#FF6E6E";
            button.style.transitionDelay = '.15s';
            warning.style.display = "inline-block";
            document.getElementById('answer-' + this.survey['correct_answer'][0]).style.transitionDelay = '.15s';
            document.getElementById('answer-' + this.survey['correct_answer'][0]).style.backgroundColor = 'lightgreen';
          } else {
            button.style.transitionDelay = '.15s';
            button.style.backgroundColor = 'lightgreen';
          }

          for (const answer of answers) {
            answer.style.pointerEvents = 'none';
          }

          for (const sectionElement of section) {
            sectionElement.classList.add('appear')
          }

        } else if (this.survey['type_answer'] === "buttons" && this.survey['correct_answer'].length > 1) {
          let index = this.choices.indexOf(id);
          if (index > -1) {
            this.choices.splice(index, 1)
            document.getElementById('answer-' + id).style.backgroundColor = "white";
          } else {
            this.choices.push(id)
            document.getElementById('answer-' + id).style.backgroundColor = "#DDDDDD";
          }

          if (this.choices.length === this.survey['correct_answer'].length) {
            this.toggle = true

            for (const correct_answer of this.survey['correct_answer']) {
              document.getElementById('answer-' + correct_answer).style.transitionDelay = '.15s';
              document.getElementById('answer-' + correct_answer).style.backgroundColor = 'lightgreen';
            }

            for (const choice of this.choices) {
              if (this.survey['correct_answer'].indexOf(choice) === -1) {
                document.getElementById('answer-' + choice).style.transitionDelay = '.15s';
                document.getElementById('answer-' + choice).style.backgroundColor = '#FF6E6E';
                warning.style.display = "inline-block";
              }
            }

            for (const answer of answers) {
              answer.style.pointerEvents = 'none';
            }

            for (const sectionElement of section) {
              sectionElement.classList.add('appear')
            }
          }
        }
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
article {
  margin: 0;
  background-image: url("../assets/img/background.svg");
  background-position: center top;
  background-size: 100.1% auto;
  background-repeat: no-repeat;
  background-position-y: -340px;
}

section {
  padding: 0 12px;
  max-width: 1200px;
  margin: 0 auto;
}

section:nth-of-type(1) {
  padding: 20px 12px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  color: var(--title);
}

section:nth-of-type(1) h1 {
  margin: 0;
  font-size: 1.8em;
}

section:nth-of-type(1) hr {
  margin: 0 50px;
  width: 1px;
  height: 60px;
  border: none;
  border-left: 2px solid var(--title);
}

#progress_bar {
  flex: 1 0 50%;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  gap: 8px 0;
}

#progress_bar p {
  margin: 0;
  font-size: 1.2em;
}

#progress {
  background: white;
  justify-content: flex-start;
  border-radius: 100px;
  align-items: center;
  position: relative;
  display: flex;
  height: 26px;
  width: 100%;
}

#progress-value {
  animation: load 2s normal forwards;
  box-shadow: 0 10px 40px -10px #fff;
  border-radius: 100px;
  background: lightgreen;
  height: 26px;
  width: 0;
  text-align: center;
  color: var(--text);
}

@keyframes load {
  from {
    width: var(--ns, 0%);
  }
  to {
    width: var(--ne, 100%);
  }
}

section:nth-of-type(2) {
  margin: 0 auto 40px;
  padding: 30px 30px 26px;
  background-color: white;
  border-radius: 16px;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
}

section:nth-of-type(2) h2 {
  margin: 0;
}

section:nth-of-type(2) p {
  max-width: 900px;
  margin: 8px auto 0;
  text-align: center;
  font-size: 1.5em;
}

#answers {
  margin-top: 36px;
  padding: 0 10px;
  display: flex;
  flex-wrap: wrap;
  gap: 0 20px;
}

#answers a {
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
  color: var(--text);
  text-decoration: none;
  box-shadow: rgba(0, 0, 0, 0.12) 0 1px 3px, rgba(0, 0, 0, 0.24) 0 1px 2px;
}

#answers a:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

.answer {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

section:nth-of-type(3) h3 {
  margin: 0;
  font-size: 1.4em;
}

section:nth-of-type(3) p, section:nth-of-type(3) li {
  margin: 8px 0 0;
  font-size: 1.1em;
  font-weight: 300;
}

section:nth-of-type(3) div {
  margin-top: 20px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 0 12px;
}

section:nth-of-type(3) p:last-of-type {
  margin: 40px 0 0;
  font-size: 1em;
  font-weight: 400;
}

#warning {
  display: none;
  height: 20px;
  width: auto;
}

section:nth-of-type(4) {
  margin: 0 auto;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

section:nth-of-type(4) a {
  font-size: 1em;
  text-align: center;
  padding: 12px 100px;
  margin: 40px 0;
  border-radius: 8px;
  color: var(--text) !important;
  text-decoration: none;
  background-color: white;
  border: 1px solid black;
  box-shadow: rgba(50, 50, 93, 0.25) 0 6px 12px -2px, rgba(0, 0, 0, 0.3) 0 3px 7px -3px;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
}

section:nth-of-type(4) a:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

.hide {
  z-index: 0 !important;
  display: none;
}

.appear {
  display: block;
  animation-duration: .4s;
  animation-name: show;
}

@keyframes show {
  from {
    transform: translateY(-46px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@media screen and (max-width: 1919px) {
  article {
    background-size: auto;
  }
}

@media screen and (max-width: 700px) {
  section:nth-of-type(1) {
    padding: 12px;
    flex-flow: column nowrap;
    gap: 20px 0;
  }

  section:nth-of-type(1) hr {
    display: none;
  }

  #progress {
    width: calc(100vw - 12px * 2) !important;
  }
}

@media screen and (max-width: 550px) {
  #answers a {
    flex: 1 0 50%;
  }
}
</style>
