import { Component, ViewChildren } from '@angular/core';
import { NgForm } from '@angular/forms';
import { ShowFlightoffersComponent } from '../show-flightoffers/show-flightoffers.component';
import { FlightResponse, FlightResponses } from '../_models/Flights/FlightResponse';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-search-flights',
  templateUrl: './search-flights.component.html',
  styleUrls: ['./search-flights.component.css']
})
export class SearchFlightsComponent {
  @ViewChildren('ShowFlightoffersComponent') child!: ShowFlightoffersComponent;
  classes = ['First class', 'Business class', 'Economy class']
  adults = [1, 2, 3, 4, 5]
  model: SearchFlightsRequest = { travelType: 0, originLocationCode: '', destinationLocationCode: '', departureDate: '', returnDate: '', adults: this.adults[0], class: this.classes[0] }
  flightsResponses: FlightResponses = {data: []};
  formSubmitted = false;
  todayDate = Date.now();

  constructor(private _flightService: FlightService) { }

  resetAll() {
    this.child.reset()
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      if (this.model.travelType == 1) this.model.returnDate = "";
      this._flightService.getFlights(this.model).subscribe(response => {
        this.flightsResponses = response;
        this.formSubmitted = true;
      })
    }
  }
}
