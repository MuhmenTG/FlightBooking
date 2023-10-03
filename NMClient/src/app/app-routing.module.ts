import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { LoginComponent } from './login/login.component';
import { AdminComponent } from './admin/admin.component';
import { AgentComponent } from './agent/agent.component';
import { FaqComponent } from './faq/faq.component';
import { MyBookingComponent } from './my-booking/my-booking.component';
import { BookingConfirmationComponent } from './booking-confirmation/booking-confirmation.component';
import { AdminFaqComponent } from './admin-faq/admin-faq.component';

const routes: Routes = [
  { path: '', component: SearchFlightsComponent },
  { path: 'work/login', component: LoginComponent },
  { path: 'admin', component: AdminComponent },
  { path: 'admin/faq', component: AdminFaqComponent },
  { path: 'showbooking', component: AdminComponent },
  { path: 'agent', component: AgentComponent },
  { path: 'faq', component: FaqComponent },
  { path: 'mybooking', component: MyBookingComponent },
  { path: 'bookingconfirmation', component: BookingConfirmationComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
