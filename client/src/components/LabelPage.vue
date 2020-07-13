<template>
  <div>
    <div class="canvas_div" id="canvas_div">
      <canvas id="label_canvas" class="canvas"></canvas>
    </div>
    <div id="contrl_panel">
      <button id="delete_last_point" @click="deleteLastPoint">删除上一个点</button>
      <button id="retype_label">确定label</button>
    </div>
    <div id="popup" :style="display">
      <div id="popup_content">
        <div>
          <span id="select_label">选择label:</span>
          <select style="width:200px" v-model="selectValue" @change="changeSelection">
            <option v-for="item in respData.labels" :value="item" :key="item" :label="item" name="labels">
            </option>
          </select>
        </div>
        <span id="button_span">
          <button id="confim_button" @click="confimButtonClick">确认</button>
          <button id="cancel_button" @click="cancelButtonClick">取消</button>
        </span>
      </div>
    </div>
    <div id="label_box">
      <div class="list-label">label列表</div>
      <div>
        <table id="label_table" class="list-table" cellspacing="0" cellpadding="1" border="1px">
          <tr>
            <td>label</td>
            <td>分类</td>
            <td>操作</td>
          </tr>
        </table>
      </div>
    </div>
    <div id="classes_box">
      <div class="list-label">分类列表</div>
        <table id="classes_table" class="list-table" cellspacing="0" cellpadding="1" border="1px">
          <tr>
            <td>Label</td>
            <td>颜色</td>
          </tr>
        </table>
    </div>
    <div id="files_box">
      <div class="list-label">文件列表</div>
      <table>
        <table id="list-table" cellspacing="0" cellpadding="1" border="1px">
          <tr>
            <td>文件名</td>
            <td>状态</td>
            <td>操作</td>
          </tr>
        </table>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      // canvas最底层画布
      canvas: null,
      // 画笔
      ctx: null,
      x: 0,
      y: 0,
      lastX: null,
      lastY: null,
      // 标注图片对象
      image: null,
      name: 'LabelPage',
      // 起始点
      startPoint: [],
      // 标注点集合
      drawingLines: [],
      // 鼠标是否在canvas内
      mouseInCanvas: false,
      canvasDiv: null,
      // canvas点图层计数器
      count: 1,
      // canvas label图层计数器
      labelCount: 0,
      // 是否能够绘画的状态
      drawSwitch: true,
      // canvas范围rect对象
      canvasArea: null,
      colors: ['yellow', 'blue', 'green', 'black'],
      // 该label是否标注完成
      labelCompelete: false,
      // 弹出层默认css
      display: 'display:none',
      selectValue: null,
      respData: {
        'labels': [],
        'images': [],
        'img_status': []
      }
    }
  },
  // 页面加载逻辑
  mounted () {
    this.initCanvas()
    window.addEventListener('click', this.mouseClick)
    this.canvasDiv = document.getElementById('canvas_div')
    // get请求获取初始数据
    this.getInitData()
  },
  methods: {
    changeImage (file) {
      let url = this.$host + '?pid=' + file
      this.canvas.width = 1280
      this.canvas.height = 720
      this.image = new Image()
      this.image.src = url
      this.image.onload = this.pictureDisplay
      this.clearLabelLayers()
      this.clearAllLayers()
      let tb = document.getElementById('label_table')
      let rowNum = tb.rows.length
      for (let i = 1; i < rowNum; i++) {
        tb.deleteRow(i)
        rowNum = rowNum - 1
        i = i - 1
      }
    },
    // 删除已标注label
    deleteLayer (id) {
      let layerId = 'label_' + id
      let labelId = 'label_record_' + id
      console.log(layerId)
      console.log(labelId)
      let layer = document.getElementById(layerId)
      let label = document.getElementById(labelId)
      console.log(layer)
      console.log(label)
      layer.remove()
      label.remove()
    },
    // 根据value查找对应数组里面的key
    findKey (obj, value, compare = (a, b) => a === b) {
      return Object.keys(obj).find(k => compare(obj[k], value))
    },
    // 获取初始化数据
    async getInitData () {
      let url = this.$host + '?type=classes'
      this.$axios.get(url).then(response => {
        if (response.data.status === 1) {
          this.$set(this, 'respData', response.data)
          this.selectValue = this.respData.labels[0]
          console.log(this.respData)
          this.updateClassesList()
          this.updateFileList()
        }
      }).catch(function (error) {
        console.log(error)
      })
    },
    // 选择分类下拉框选择事件
    changeSelection (event) {
      this.selectValue = event.target.value
    },
    // 更新分类列表
    updateClassesList () {
      let clt = document.getElementById('classes_table')
      for (let i = 0; i < Object.keys(this.respData.labels).length; i++) {
        let tr = document.createElement('tr')
        let td1 = document.createElement('td')
        td1.innerText = this.respData.labels[i]
        let td2 = document.createElement('td')
        let div = document.createElement('div')
        div.style = 'margin-left:50px;width:10px;height:10px;background:' + this.colors[i] + ';'
        td2.appendChild(div)
        tr.appendChild(td1)
        tr.appendChild(td2)
        clt.appendChild(tr)
      }
    },
    // 更新文件列表
    updateFileList () {
      let clt = document.getElementById('list-table')
      console.log(this.respData.images)
      for (let i = 0; i < Object.keys(this.respData.images).length; i++) {
        let tr = document.createElement('tr')
        let td1 = document.createElement('td')
        td1.innerText = this.respData.images[i]
        let td2 = document.createElement('td')
        if (this.respData['img_status'][i] === '') {
          td2.innerText = '未标注'
          td2.style = 'color:red;'
        } else {
          td2.innerText = this.respData['img_status'][i] + '标注中'
          td2.style = ''
        }
        let td3 = document.createElement('td')
        let but = document.createElement('button')
        but.innerText = '打开'
        but.onclick = () => this.changeImage(this.respData.images[i])
        td3.appendChild(but)
        tr.appendChild(td1)
        tr.appendChild(td2)
        tr.appendChild(td3)
        clt.appendChild(tr)
      }
    },
    // 更新label列表
    addNewLabel () {
      let clt = document.getElementById('label_table')
      let tr = document.createElement('tr')
      let td1 = document.createElement('td')
      td1.innerText = 'label_' + (this.labelCount + 1)
      tr.id = 'label_record_' + (this.labelCount + 1)
      let td2 = document.createElement('td')
      td2.innerText = this.selectValue
      let td3 = document.createElement('td')
      let but = document.createElement('button')
      but.innerText = '删除'
      but.id = this.labelCount + 1
      but.onclick = () => this.deleteLayer(but.id)
      td3.appendChild(but)
      tr.appendChild(td1)
      tr.appendChild(td2)
      tr.appendChild(td3)
      clt.appendChild(tr)
    },
    // 界面初始化函数
    initCanvas () {
      this.canvas = document.getElementById('label_canvas')
      this.ctx = this.canvas.getContext('2d')
      // 必须设置和css是两样东西
      this.canvas.width = 1280
      this.canvas.height = 720
      this.image = new Image()
      this.image.src = this.$host + '?pid=0.jpg'
      this.image.onload = this.pictureDisplay
    },
    // 确认按钮
    confimButtonClick () {
      this.addNewLabel()
      this.updateCanvasLayers(this.colors[this.findKey(this.respData.labels, this.selectValue)])
      this.display = 'display:none'
      this.labelCompelete = false
      this.lastX = null
      this.lastY = null
    },
    // 鼠标点击canvas函数
    mouseClick (evt) {
      if (this.drawSwitch === true && this.labelCompelete === false) {
        this.x = evt.offsetX
        this.y = evt.offsetY
        if (evt.path[0].className !== 'canvas') {
          return
        }
        if (this.x < this.canvasArea.left || this.x > (this.canvasArea.left + this.canvasArea.width)) {
          return
        }
        if (this.y < this.canvasArea.top || this.y > (this.canvasArea.top + this.canvasArea.height)) {
          return
        }
        if (Math.abs(this.x - this.startPoint[0]) <= 5 && Math.abs(this.y - this.startPoint[1]) <= 5 && this.drawingLines.length > 1) {
          this.x = this.startPoint[0]
          this.y = this.startPoint[1]
          this.labelCompelete = true
        }
        let ctx = this.newCanvasLayer('canvas', 'canvas', this.count)
        this.drawPoint(ctx, this.x, this.y, 3, 'red')
        if (this.lastX != null && this.lastY != null) {
          this.drawLine(ctx, this.x, this.y, this.lastX, this.lastY, 'red')
        } else {
          this.startPoint = [this.x, this.y]
        }
        this.lastX = this.x
        this.lastY = this.y
        this.drawingLines.push([this.x, this.y])
        this.count += 1
        if (this.labelCompelete) {
          this.display = ' '
        }
      }
    },
    // 创建新图层
    newCanvasLayer (id, className, number) {
      let newCanvas = document.createElement('canvas')
      newCanvas.width = this.canvas.width
      newCanvas.height = this.canvas.height
      newCanvas.id = id + number
      newCanvas.className = className
      newCanvas.style = 'position: absolute;z-index:' + (this.count + 100) + ';'
      this.canvasDiv.appendChild(newCanvas)
      return newCanvas.getContext('2d')
    },
    // 图像加载完成回调函数
    pictureDisplay () {
      this.ctx.drawImage(this.image, 0, 0, this.canvas.width, this.canvas.height)
      this.canvasArea = this.canvas.getBoundingClientRect()
    },
    // 画线事件
    drawLine (ctx, x1, y1, x2, y2, color) {
      ctx.save()
      ctx.beginPath()
      ctx.lineCap = 'round'
      ctx.lineJoin = 'round'
      ctx.createLinearGradient(x1, y1, x2, y2)
      ctx.moveTo(x1, y1)
      ctx.lineTo(x2, y2)
      ctx.closePath()
      ctx.strokeStyle = color
      ctx.stroke()
      ctx.restore()
    },
    // 删除最后一个点的点击事件
    deleteLastPoint () {
      if (this.drawingLines.length !== 0) {
        this.drawingLines.pop()
        let delNode = document.getElementById('canvas' + (this.count - 1))
        delNode.remove()
        this.count -= 1
        this.labelCompelete = false
        this.display = 'display:none'
        if (this.drawingLines.hasOwnProperty(this.count - 2)) {
          this.lastX = this.drawingLines[this.count - 2][0]
          this.lastY = this.drawingLines[this.count - 2][1]
        } else {
          this.lastX = null
          this.lastY = null
        }
      }
    },
    // 取消按钮点击事件
    cancelButtonClick () {
      this.display = 'display:none'
    },
    // 画点函数
    drawPoint (ctx, x, y, r, color) {
      ctx.fillStyle = color
      ctx.beginPath()
      ctx.arc(x, y, r, 0, Math.PI * 2)
      ctx.fill()
      ctx.closePath()
    },
    // 合并canvas图层函数
    updateCanvasLayers (color) {
      this.labelCount += 1
      let ctx = this.newCanvasLayer('label_', 'canvas', this.labelCount)
      for (let i = 0; i < this.drawingLines.length; i++) {
        this.drawPoint(ctx, this.drawingLines[i][0], this.drawingLines[i][1], 3, color)
        if (i !== 0) {
          this.drawLine(ctx, this.lastX, this.lastY, this.drawingLines[i][0], this.drawingLines[i][1], color)
        }
        this.lastX = this.drawingLines[i][0]
        this.lastY = this.drawingLines[i][1]
      }
      this.clearLabelLayers()
    },
    // 清空标注的图层
    clearLabelLayers () {
      for (let i = 1; i < this.count; i++) {
        let c = document.getElementById('canvas' + i)
        c.remove()
      }
      this.count = 1
      this.drawingLines = []
    },
    clearAllLayers () {
      for (let i = 1; i <= this.labelCount; i++) {
        let c = document.getElementById('label_' + i)
        c.remove()
      }
      this.labelCount = 0
      this.drawingLines = []
    }
  }
}
</script>

<style scoped>
  .canvas_div {
    position: relative;
  }
  .canvas {
    position: absolute;
  }
  #delete_last_point{
    margin-top: 750px;
    height: 40px;
    /*padding-top: 800px;*/
  }

  #popup{
    width: 30%;
    height: 30%;
    background: black;
    position: absolute;
    top:200px;
    left:400px;
    z-index: 999;
    opacity:0.8;
  }
  canvas {
    position: absolute;
    background:rgba(255,255,255,0);
    z-index:1;
    top:0px;
    left:0px;
    width: 1280px;
    height: 720px;
  }
  #select_label{
    color: aliceblue;
  }
  #popup_content{
    margin-top: 100px;
    text-align: center;
  }
  #confim_button{
    width: 100px;
    height: 30px;
    margin-top: 10px;
  }
  #retype_label{
    margin-top: 750px;
    height: 40px;
  }
  #cancel_button{
    width: 100px;
    height: 30px;
    margin-left: 30px;
    margin-top: 10px;
  }
  #contrl_panel{
    background: black;
    height: 968px;
    /*margin-top: 720px;*/
  }
  #label_box{
    width: 250px;
    height: 400px;
    position: absolute;
    background: aliceblue;
    top: 320px;
    left: 1280px;
  }
  #classes_box{
    width: 250px;
    height: 320px;
    position: absolute;
    background: aliceblue;
    top: 0px;
    left: 1280px;
  }
  #files_box{
    width: 300px;
    height: 720px;
    position: absolute;
    background: aliceblue;
    top: 0px;
    left: 1535px;
    overFlow-x:auto;
  }
  .list-label{
    width: 100%;
    text-align: center;
    background: #045e0b;
    color: #f0f8ff;
  }
  table,td{
    border-collapse: collapse;
    border: 1px solid #000;
    text-align: center;
  }
  table{
    width: 100%;
  }
  td{
    width: 33%;
  }
</style>
