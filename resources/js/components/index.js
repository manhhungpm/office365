import Vue from 'vue'
import Child from './Child'
import {HasError, AlertError, AlertSuccess} from 'vform'

/*
 |--------------------------------------------------------------------------
 | Common
 |--------------------------------------------------------------------------
 */
import Dropdown from './common/Dropdown'
import DynamicTag from './common/DynamicTag'
import VButton from './common/VButton'
import Alert from './common/Alert'
import PerfectScrollbar from './common/PerfectScrollbar'
import ThePortlet from './common/ThePortlet'
import DataTable from './common/DataTable'
import TheModal from './common/TheModal'
import FormControl from './common/FormControl'
import Select2 from './common/Select2'
import DatePicker from './common/DatePicker'
import TheHighcharts from './common/TheHighcharts'
import DownloadButton from './common/DownloadButton'

/*
 |--------------------------------------------------------------------------
 | Elements
 |--------------------------------------------------------------------------
 */
import Notification from './elements/Notification'
import LanguageChosen from './elements/LanguageChosen'
import ProfileActions from './elements/ProfileActions'
import UserChosen from './elements/UserChosen'
import ResellerChosen from './elements/ResellerChosen'
import AccountChosen from './elements/AccountChosen'
import DomainChosen from './elements/DomainChosen'

// Components that are registered global.
[
    Child,
    HasError,
    AlertError,
    AlertSuccess,
    Dropdown,
    DynamicTag,
    VButton,
    Alert,
    PerfectScrollbar,
    ThePortlet,
    DataTable,
    TheModal,
    FormControl,
    Select2,
    DatePicker,
    TheHighcharts,
    DownloadButton,

    Notification,
    LanguageChosen,
    ProfileActions,
    UserChosen,
    ResellerChosen,
    AccountChosen,
    DomainChosen,
].forEach(Component => {
    Vue.component(Component.name, Component)
})
