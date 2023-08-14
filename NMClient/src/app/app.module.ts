import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { FormsModule } from '@angular/forms';
import { ShowFlightoffersComponent } from './show-flightoffers/show-flightoffers.component';
import { BookFlightComponent } from './book-flight/book-flight.component';
import { PaymentComponent } from './payment/payment.component';
import { SearchHotelsComponent } from './search-hotels/search-hotels.component';
import { AdminComponent } from './admin/admin.component';
import { AgentComponent } from './agent/agent.component';
import { LoginComponent } from './login/login.component';
import { ShowBookingComponent } from './show-booking/show-booking.component';
import { FaqComponent } from './faq/faq.component';
import { MyBookingComponent } from './my-booking/my-booking.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

@NgModule({
  declarations: [
    AppComponent,
    SearchFlightsComponent,
    ShowFlightoffersComponent,
    BookFlightComponent,
    PaymentComponent,
    SearchHotelsComponent,
    AdminComponent,
    AgentComponent,
    LoginComponent,
    ShowBookingComponent,
    FaqComponent,
    MyBookingComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    BrowserAnimationsModule,
    MatProgressSpinnerModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
