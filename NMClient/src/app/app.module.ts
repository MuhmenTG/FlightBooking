import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { FormsModule } from '@angular/forms';
import { ShowFlightofferComponent } from './show-flightoffer/show-flightoffer.component';

@NgModule({
  declarations: [
    AppComponent,
    SearchFlightsComponent,
    ShowFlightofferComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
