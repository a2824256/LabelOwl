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
          <Select style="width:200px" v-model="formItem.labels">
            <Option v-for="item in formItem.stateList" :value="item.value" :key="item.value" name="labels">{{
              item.label }}
            </Option>
          </Select>
        </div>
        <span id="button_span">
          <button id="confim_button" @click="cancelButtonClick">确认</button>
          <button id="cancel_button" @click="cancelButtonClick">取消</button>
        </span>
      </div>
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
      colors: ['red', 'blue', 'green', 'yellow'],
      // 该label是否标注完成
      labelCompelete: false,
      // 弹出层默认css
      display: 'display:none',
      formItem: {
        stateList: [
          {
            value: '0',
            label: 'consil'
          },
          {
            value: '1',
            label: 'mouth'
          },
          {
            value: '2',
            label: 'pp-wall'
          }
        ]
      }
    }
  },
  // 页面加载逻辑
  mounted () {
    this.initCanvas()
    window.addEventListener('click', this.mouseClick)
    this.canvasDiv = document.getElementById('canvas_div')
  },
  methods: {
    // 界面初始化函数
    initCanvas () {
      this.canvas = document.getElementById('label_canvas')
      this.ctx = this.canvas.getContext('2d')
      // 必须设置和css是两样东西
      this.canvas.width = 1280
      this.canvas.height = 720
      this.image = new Image()
      this.image.src = 'http://localhost:9090/static/223.jpg'
      this.image.onload = this.pictureDisplay
    },
    // 鼠标点击canvas函数
    mouseClick (evt) {
      if (this.drawSwitch === true && this.labelCompelete === false) {
        this.x = evt.offsetX
        this.y = evt.offsetY
        if (evt.path[0].className !== 'canvas') return
        if (this.x < this.canvasArea.left || this.x > (this.canvasArea.left + this.canvasArea.width)) return
        if (this.y < this.canvasArea.top || this.y > (this.canvasArea.top + this.canvasArea.height)) return
        if (Math.abs(this.x - this.startPoint[0]) <= 5 && Math.abs(this.y - this.startPoint[1]) <= 5 && this.drawingLines.length > 1) {
          this.x = this.startPoint[0]
          this.y = this.startPoint[1]
          this.labelCompelete = true
        }
        let newCanvas = document.createElement('canvas')
        newCanvas.width = this.canvas.width
        newCanvas.height = this.canvas.height
        newCanvas.id = 'canvas' + this.count
        newCanvas.className = 'canvas'
        newCanvas.style = 'position: absolute;z-index:' + (this.count + 100) + ';'
        this.canvasDiv.appendChild(newCanvas)
        let ctx = newCanvas.getContext('2d')
        this.drawPoint(ctx, this.x, this.y, 4)
        if (this.lastX != null && this.lastY != null) {
          this.drawLine(ctx, this.x, this.y, this.lastX, this.lastY)
        } else {
          this.startPoint = [this.x, this.y]
        }
        this.lastX = this.x
        this.lastY = this.y
        this.drawingLines.push([this.x, this.y])
        this.count += 1
        if (this.labelCompelete) {
          this.display = ' '
          this.updateCanvasLayers()
        }
      }
    },
    // 图像加载完成回调函数
    pictureDisplay () {
      this.ctx.drawImage(this.image, 0, 0, this.canvas.width, this.canvas.height)
      this.canvasArea = this.canvas.getBoundingClientRect()
    },
    // 画线事件
    drawLine (ctx, x1, y1, x2, y2) {
      ctx.save()
      ctx.beginPath()
      ctx.lineCap = 'round'
      ctx.lineJoin = 'round'
      ctx.createLinearGradient(x1, y1, x2, y2)
      ctx.moveTo(x1, y1)
      ctx.lineTo(x2, y2)
      ctx.closePath()
      ctx.strokeStyle = 'red'
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
    drawPoint (ctx, x, y, r) {
      ctx.fillStyle = 'red'
      ctx.beginPath()
      ctx.arc(x, y, r, 0, Math.PI * 2)
      ctx.fill()
      ctx.closePath()
    },
    // 标注点集合遍历函数
    continueLinesIter (item, index) {
      console.log(item + ',' + index)
    },
    // 合并canvas图层函数
    updateCanvasLayers () {
      this.drawingLines.forEach(this.continueLinesIter)
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
    height: 1000px;
    /*margin-top: 720px;*/
  }
</style>
