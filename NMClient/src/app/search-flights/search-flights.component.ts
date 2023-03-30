import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { FlightResponse } from '../_models/Flights/FlightResponse';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { SearchFlightsResponses } from '../_models/Flights/SearchFlightsResponses';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-search-flights',
  templateUrl: './search-flights.component.html',
  styleUrls: ['./search-flights.component.css']
})
export class SearchFlightsComponent implements OnInit {
  classes = ['First class', 'Business class', 'Economy class']
  adults = [1, 2, 3, 4, 5]
  model: SearchFlightsRequest = { travelType: 0, originLocationCode: '', destinationLocationCode: '', departureDate: '', returnDate: '', adults: this.adults[0], class: this.classes[0] }
  flightsResponses: FlightResponse[] = [];
  formSubmitted = false;
  isLoaded = false;
  todayDate = Date.now();

  constructor(private _flightService: FlightService) { }

  ngOnInit(): void {
  }

  submitForm(form: NgForm) {
    this.isLoaded = false;

    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      if (this.model.travelType == 1) this.model.returnDate = "";
      this._flightService.getFlights(this.model).subscribe(responses => {
        this.flightsResponses = [];
        responses.data.forEach(flightOffer => {
          this.flightsResponses.push(flightOffer);
          this.isLoaded = true;
          this.formSubmitted = true;
        });
        return true;
      })
      return true;
    }
  }
}
