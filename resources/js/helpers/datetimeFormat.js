import moment from 'moment'

export const toNormalDate = datetimeStr => {
  if (moment(datetimeStr, 'DD/MM/YYYY').isValid())
    return datetimeStr
  else {
    if (moment(datetimeStr, 'YYYY-MM-DD').isValid()) {
      return moment(datetimeStr, 'YYYY-MM-DD').format('DD/MM/YYYY')
    } else if (moment(datetimeStr, 'YYYY-MM-DD HH:mm:ss').isValid()) {
      return moment(datetimeStr, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY')
    } else return '-'
  }

}

export const isValidDate = datetimeStr => {
  return moment(datetimeStr).isValid()
}
