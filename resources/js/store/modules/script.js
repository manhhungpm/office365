export const state = () => {
  return {
    queryState: {},
    selectedNode: null,
    checkedNode: null,
    infos: [], //mảng các thông tin truy vấn được
    entityTypes: [],
    botId: null,
    nodeArr: []
  }
}

export const mutations = {
  setSelectedNode(state, selectedNode) {
    state.selectedNode = selectedNode
  },
  updateInfos(state, infos) {
    state.infos = infos
  },
  setEntityTypes(state, entityTypes) {
    state.entityTypes = entityTypes
  },
  setBotId(state, botId) {
    state.botId = botId
  },
  updateSelectedNode(state, selectedNode) {
    state.selectedNode = Object.assign(state.selectedNode, selectedNode)
  },
  setNodeArr(state, nodeArr){
    state.nodeArr = nodeArr
  },
  setQueryState(state, queryState){
    state.queryState = queryState
  },
  setCheckedNode(state, checkedNode){
    state.checkedNode = checkedNode
  }
}
