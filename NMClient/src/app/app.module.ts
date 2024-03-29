import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ShowFlightoffersComponent } from './show-flightoffers/show-flightoffers.component';
import { BookFlightComponent } from './book-flight/book-flight.component';
import { PaymentComponent } from './payment/payment.component';
import { AdminComponent } from './admin/admin.component';
import { AgentComponent } from './agent/agent.component';
import { LoginComponent } from './login/login.component';
import { ShowBookingComponent } from './show-booking/show-booking.component';
import { FaqComponent } from './faq/faq.component';
import { MyBookingComponent } from './my-booking/my-booking.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { BookingConfirmationComponent } from './booking-confirmation/booking-confirmation.component';
import { NgxPrintModule } from 'ngx-print';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { AsyncPipe, JsonPipe, NgFor, NgIf } from '@angular/common';
import { MAT_RADIO_DEFAULT_OPTIONS, MatRadioModule } from '@angular/material/radio';
import { MatNativeDateModule } from '@angular/material/core';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatSelectModule } from '@angular/material/select';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { MatButtonModule } from '@angular/material/button';
import { MatMenuModule } from '@angular/material/menu';
import { MAT_SNACK_BAR_DEFAULT_OPTIONS, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatExpansionModule } from '@angular/material/expansion';
import { AdminFaqComponent } from './admin-faq/admin-faq.component';
import { EnquiryComponent } from './enquiry/enquiry.component';
import { AgentEnquiryComponent } from './agent-enquiry/agent-enquiry.component';
import { MatSidenavModule } from '@angular/material/sidenav';

@NgModule({
  declarations: [
    AppComponent,
    SearchFlightsComponent,
    ShowFlightoffersComponent,
    BookFlightComponent,
    PaymentComponent,
    AdminComponent,
    AgentComponent,
    LoginComponent,
    ShowBookingComponent,
    FaqComponent,
    MyBookingComponent,
    BookingConfirmationComponent,
    AdminFaqComponent,
    EnquiryComponent,
    AgentEnquiryComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    BrowserAnimationsModule,
    MatProgressSpinnerModule,
    NgxPrintModule,
    MatFormFieldModule,
    MatInputModule,
    MatAutocompleteModule,
    ReactiveFormsModule,
    NgFor,
    NgIf,
    AsyncPipe,
    JsonPipe,
    MatRadioModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatSelectModule,
    MatIconModule,
    MatDividerModule,
    MatButtonModule,
    MatMenuModule,
    MatSnackBarModule,
    MatCheckboxModule,
    MatExpansionModule,
    MatSidenavModule
  ],
  providers: [
    {
      provide: MAT_RADIO_DEFAULT_OPTIONS,
      useValue: { color: 'primary' }
    },
    {
      provide: MAT_SNACK_BAR_DEFAULT_OPTIONS,
      useValue: { duration: 2500 }
    }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }