export const filterTree = (menu, role) => {
  const filtered = menu.filter(e => {
    return e.role === role || e.role === undefined
  }).map( e => {
    if(e.children) {
      e.children = filterTree(e.children, role)
    }
    return e
  })
  return filtered
}