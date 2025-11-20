import HomeController from './HomeController'
import NewsController from './NewsController'
import EventController from './EventController'
import ContactController from './ContactController'

const Public = {
    HomeController: Object.assign(HomeController, HomeController),
    NewsController: Object.assign(NewsController, NewsController),
    EventController: Object.assign(EventController, EventController),
    ContactController: Object.assign(ContactController, ContactController),
}

export default Public