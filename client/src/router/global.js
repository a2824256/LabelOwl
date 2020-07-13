const host = 'http://localhost:2347'

export default{
  host,
  ws: {},
  setWs: function (newWs) {
    this.ws = newWs
  }
}
