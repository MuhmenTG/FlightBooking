import { Component, Input, OnInit } from '@angular/core';
import { CustomerInfo } from '../_models/CustomerInfo';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightResponse } from '../_models/Flights/FlightResponse';
import { SearchFlightsResponses } from '../_models/Flights/SearchFlightsResponses';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-show-flightoffers',
  templateUrl: './show-flightoffers.component.html',
  styleUrls: ['./show-flightoffers.component.css']
})
export class ShowFlightoffersComponent {
  @Input() offers!: FlightResponse[];
  flightInfo = {} as FlightInfoResponse
  customerInfo: CustomerInfo = { firstName: "Bent", lastName: "Bentesen", email: "test@email.com", dateOfBirth: Date(), passengerType: "Adult" }

  constructor(private _flightService: FlightService) { }

  chooseFlight(id: string) {
    this._flightService.getFlightInfo(this.offers[parseInt(id)]).subscribe(info => {
      info.data.flightOffers[0].passengers = [];
      info.data.flightOffers[0].passengers.push(this.customerInfo);
      this._flightService.getFlightConfirmation(info.data.flightOffers[0]).subscribe(x => console.log(JSON.stringify(x)))
    })
  }
}
