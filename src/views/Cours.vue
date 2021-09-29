<template>
  <article>
    <section>
      <h1> Cours 1 </h1>
    </section>
    <section>
      <h2>Chapitre</h2>
      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque euismod risus eu nisl pretium condimentum. Vivamus malesuada nisi vel ex cursus, vitae mattis risus blandit. Donec feugiat lobortis facilisis. Donec lacus felis, bibendum in enim ut, iaculis consequat purus. Suspendisse potenti. Vivamus dignissim ante ac ligula cursus congue. Quisque nunc diam, sollicitudin vel justo sit amet, fermentum hendrerit augue. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris mollis sapien arcu, in ornare quam maximus vitae. Vestibulum lacinia, nibh nec posuere tincidunt, lacus quam pharetra lectus, at maximus quam nunc a magna.
        Etiam venenatis lobortis elit nec sollicitudin. Fusce neque magna, hendrerit non tellus ut, efficitur scelerisque justo. Interdum et malesuada fames ac ante ipsum primis in faucibus. Maecenas dapibus laoreet tristique. Sed vel viverra metus. Donec pellentesque, augue nec ullamcorper pretium, ante augue lobortis odio, nec interdum felis massa ut justo. Aliquam condimentum pulvinar consequat. Vestibulum id mi vel lorem tempor blandit id eu orci. Duis ullamcorper, turpis sit amet rhoncus lobortis, libero neque tincidunt purus, sit amet varius lorem ipsum efficitur ex. Cras in rhoncus lectus.
        Vivamus id placerat mauris. Duis eget aliquam quam, ac ullamcorper enim. Proin commodo, lacus ac venenatis maximus, sem lectus mollis dui, commodo ultricies risus lacus nec orci. Maecenas auctor tempor metus, ut mollis libero pharetra vulputate. Etiam sodales sem sit amet orci mattis efficitur. Pellentesque vitae velit orci. Nullam elementum quam quis dui tempor, nec auctor justo sagittis. Curabitur condimentum ipsum in massa laoreet pulvinar.
        Sed a elementum augue. Aenean vel nibh in justo facilisis finibus. Vivamus tristique pretium nunc vel sagittis. Aliquam nulla mi, malesuada eu ultrices non, egestas eget ligula. Nullam vulputate nibh leo, quis condimentum dui pharetra at. Phasellus tempus nunc et ornare placerat. In eget quam vulputate, semper felis quis, accumsan risus. Fusce porta neque ullamcorper purus posuere dictum. Donec accumsan viverra dolor ac interdum. Proin dictum lacus ligula, eget rutrum sapien pharetra non.
        Nunc dolor neque, lobortis nec interdum et, finibus non magna. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Etiam scelerisque congue purus, nec tincidunt leo. Praesent et auctor felis. Integer sit amet diam purus. Cras eget nisi varius, fringilla neque eu, dictum erat. Sed euismod eget ante ut commodo. Nullam quis justo vel nisi congue pellentesque. Phasellus in magna id tellus viverra pharetra quis eu turpis. Aliquam erat volutpat. Vivamus mollis odio id aliquam sollicitudin. Vivamus in consectetur mi. Curabitur at elit iaculis, porta sem eget, ultrices nibh.
      </p>
    </section>
    <section>
      <a href="#">Cours précédent</a>
      <a href="#">Cours suivant</a>
    </section>
  </article>
</template>

<script>
export default {
  name: "Cours"
}
</script>

<style scoped>
article {
  background-image: url("../assets/img/background.svg");
  background-position: center top;
  background-size: 100.1% auto;
  background-repeat: no-repeat;
  background-position-y: -340px;
}

section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 12px;
}

section:nth-of-type(1) {
  padding: 30px 0;
}

section:nth-of-type(1) h1 {
  margin: 0;
  font-size: 2.2em;
  color: var(--title);
  text-align: center;
  font-family: "Montserrat", Roboto, Arial, sans-serif;
}

section:nth-of-type(2) {
  margin: 0 auto;
  padding: 26px;
  background-color: white;
  border-radius: 16px;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
}

section:nth-of-type(2) h2 {
  margin: 0;
}

section:nth-of-type(2) p {
  margin: 12px 0 0;
  font-weight: 300;
  text-align: justify;
}

section:nth-of-type(3) {
  margin: 40px auto;
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  justify-content: space-evenly;
  gap: 20px 0;
}

section:nth-of-type(3) a {
  font-size: 1em;
  text-align: center;
  max-width: 300px;
  width: 100%;
  padding: 12px 0;
  border-radius: 8px;
  color: var(--text) !important;
  text-decoration: none;
  background-color: white;
  border: 1px solid black;
  box-shadow: rgba(50, 50, 93, 0.25) 0 6px 12px -2px, rgba(0, 0, 0, 0.3) 0 3px 7px -3px;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
}

section:nth-of-type(3) a:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

@media screen and (max-width: 1919px) {
  article {
    background-size: auto;
  }
}

@media screen and (max-width: 500px) {
  section:nth-of-type(3) a {
    max-width: 100%;
  }
}
</style>