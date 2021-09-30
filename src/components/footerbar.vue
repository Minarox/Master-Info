<template>
  <footer>
    <p>Copyright  | <a href="/">SeQrity.fr</a></p>
  </footer>
</template>

<script>
export default {
  name: "footerbar"
}
</script>

<style scoped>
footer {
  padding: 12px;
  background-color: #595959;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
}

p, a {
  margin: 0;
  padding: 0;
  color: var(--title);
  text-decoration: none;
  font-size: .94em;
}
</style>