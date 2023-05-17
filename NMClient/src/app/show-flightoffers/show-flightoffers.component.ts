import { Component, Input } from '@angular/core';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightResponses } from '../_models/Flights/FlightResponse';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-show-flightoffers',
  templateUrl: './show-flightoffers.component.html',
  styleUrls: ['./show-flightoffers.component.css']
})
export class ShowFlightoffersComponent {
  @Input() offers!: FlightResponses;
  @Input() formSubmitted!: boolean;
  flightInfo = {} as FlightInfoResponse;
  flightChosen: boolean = false;
  moreOffers: boolean = true;
  shownOffers: number = 10;


  constructor(private _flightService: FlightService) { }

  chooseFlight(id: string) {
    this._flightService.getFlightInfo(this.offers.data[parseInt(id) - 1]).subscribe(info => {
      this.flightChosen = true;
      this.flightInfo = info;
    })
  }



  reset() {
    this.flightChosen = false;
  }
}
